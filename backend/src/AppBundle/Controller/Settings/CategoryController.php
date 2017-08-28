<?php

namespace AppBundle\Controller\Settings;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Context;
use AppBundle\Form\Settings\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Route("categories")
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Settings")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/{context}/", name="categories")
     * @Breadcrumb("{context.id}")
     */
    public function indexAction(Context $context, Request $request)
    {
        if($request->isXmlHttpRequest()){

            $categories = $this->getCategoryManager()->findBy([
                'context' => $context,
                'account' => $this->getCurrentAccount()
            ], ['position' => 'ASC']);

            return $this->render('settings.categories\index_content', [
                'categories' => $categories
            ]);
        }
        
        return $this->render('settings.categories\index', [
            'context' => $context
        ]);
    }

    /**
     * @Route("/{context}/create", name="categories_create")
     */
    public function createAction(Context $context, Request $request)
    {
        $manager = $this->getCategoryManager();
        $category = $manager->create();

        $category->setContext($context);

        $form = $this->createCategoryForm($category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $account = $this->getCurrentAccount();

            $category
                ->setAccount($account)
                ->setPosition($account->getCategories($context)->count()+1)
            ;

            $manager->save($category);

            return $this->jsonResponse([], Response::HTTP_CREATED);
        }

        return $this->render('settings.categories\form', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/{token}/update", name="categories_update")
     */
    public function updateAction(Request $request, Category $category)
    {
        $this->checkAccess($category);

        $manager = $this->getCategoryManager();

        $form = $this->createCategoryForm($category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->save($category);

            return $this->jsonResponse([], Response::HTTP_OK);
        }

        return $this->render('settings.categories\form', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/position/sort", name="categories_position")
     * @Method("post")
     */
    public function positionAction(Request $request)
    {
        $elements = $request->request->get('elements');
        if(count($elements)){

            $account = $this->getCurrentAccount();
            $manager = $this->getCategoryManager();
            $accessChecked = false;

            foreach($elements as $key => $element){

                $category = $manager->findOneBy([
                    'account' => $account,
                    'token' => $element['token']
                ]);

                if($category instanceof Category){

                    if(!$accessChecked){
                        $this->checkAccess($category);
                        $accessChecked = true;
                    }

                    $category->setPosition($element['position']);
                    $manager->save($category, ($key == count($elements)-1));
                }
            }
        }

        return $this->jsonResponse([], Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/{token}/delete", name="categories_delete")
     * @Method("delete")
     */
    public function deleteAction(Category $category)
    {
        $this->checkAccess($category);

        $throwError = function($message, $args)
        {
            $error = $this->translate($message, $args);
            return $this->json(['error' => $error], Response::HTTP_IM_USED);
        };

        $getProjects = function($category)
        {
            return $this->manager('project')->findBy(Array(
                'stage' => $category
            ));
        };

        $getContacts = function($category)
        {
            return $this->manager('customer')->findBy(Array(
                'category' => $category
            ));
        };

        $getAccount = function($context)
        {
            return $this->account()->getCategories($context);
        };

        $translate = function($context)
        {
            return $this->translate($context->getId());
        };

        $context = $category->getContext();

        $projects = $getProjects($category);
        $countProjects = count($projects);

        if (0 < $countProjects) {
            return $throwError('Sales step in use', [
                '%count%' => $countProjects
            ]);
        }

        $contacts = $getContacts($category);
        $countContacts = count($contacts);

        if (0 < $countContacts) {
            return $throwError('Contacts category in use', [
                '%count%' => $countContacts
            ]);
        }

        $account = $getAccount($context);

        if (1 == $account->count()) {
            return $throwError('The account must have one', [
                '%category%' => $translate($context)
            ]);
        }

        $this->manager('category')->delete($category);

        return $this->json([], Response::HTTP_OK);
    }

    /**
     * @param Category $category
     * @return \Symfony\Component\Form\Form
     */
    private function createCategoryForm(Category &$category)
    {
        $url = $category->getToken()
            ? $this->generateUrl('categories_update', ['token' => $category->getToken()])
            : $this->generateUrl('categories_create', ['context' => $category->getContext()->getId()]) ;

        return $this->createForm(CategoryType::class, $category, [
            'action' => $url,
            'method' => 'post'
        ]);
    }

    /**
     * @param CategoryInterface $category
     */
    private function checkAccess(CategoryInterface $category)
    {
        $this->denyAccessUnlessGranted('edit', $category);
    }
}
