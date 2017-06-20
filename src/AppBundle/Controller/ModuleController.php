<?php

namespace AppBundle\Controller;

//use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\KitComponent;
use AppBundle\Entity\Component\ModuleInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\Component\ModuleType;
use AppBundle\Entity\Component\Module;

/**
 * Class ModuleController
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Route("module")
 *
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Modules", route={"name"="module_index"})
 */
class ModuleController extends ComponentController
{
    /**
     * @Route("/", name="module_index")
     */
    public function indexAction(Request $request)
    {
        $data = $this->prepareIndexData();

        return $this->render('component.index', $data);
    }

    /**
     * @Breadcrumb("Add")
     * @Route("/create", name="module_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->getModuleManager();

        /** @var Module $module */
        $module = $manager->create();
        $module->toViewMode();

        if ($this->getUser()->isOwner()) {
            $module->setAccount($this->getCurrentAccount());
        }

        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->saveComponent($module, $request);

            /*$status = $this->getUser()->isAdmin() ? Module::STATUS_VALIDATED : Module::STATUS_FEATURED ;
            $module->setStatus($status);
            $manager->save($module);
            $this->uploadComponentFiles($module, $request->files);
            $manager->save($module);*/
            //$this->setNotice("Módulo cadastrado com sucesso!");
            //return $this->redirectToRoute('module_index');
        }

        return $this->render("module.form", [
            'form' => $form->createView(),
            'module' => $module
        ]);
    }

    /**
     * @Breadcrumb("{module.model}")
     * @Route("/{token}/update", name="module_update")
     * @Method({"get","post"})
     */
    public function updateAction(Request $request, Module $module)
    {
        $this->checkAccess($module);

        $module->toViewMode();
        
        //$manager = $this->getModuleManager();
        $form = $this->createForm(ModuleType::class, $module, [
            'is_validation' => $this->getUser()->isAdmin()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->saveComponent($module, $request);

            /*$response = $this->checkComponentStatusChange($module, $request);

            if($response instanceof Response){

                $this->uploadComponentFiles($module, $request->files);

                return $response;
            }

            $this->uploadComponentFiles($module, $request->files);

            $manager->save($module);

            $this->setNotice("Módulo Atualizado com sucesso!");

            if(null != $referer = $this->restore('referer')){
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('module_index');
            */
        }

        if($request->isMethod('get')) {
            $this->store('referer', $request->server->get('HTTP_REFERER'));
        }

        return $this->render("module.form", [
            'form' => $form->createView(),
            'module' => $module
        ]);
    }

    /**
     * @Route("/{token}/delete", name="module_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, Module $module)
    {
        $this->checkAccess($module);

        $usages = $this->getDoctrine()
            ->getManager()
            ->getRepository(KitComponent::class)
            ->findBy(['module' => $module]);

        if(0 != $count = count($usages)){
            return $this->jsonResponse([
                'error' => sprintf('Este módulo está sendo utilizado em %s kits', $count)
            ], Response::HTTP_IM_USED);
        }
        
        $this->getModuleManager()->delete($module);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Breadcrumb("{module.model}")
     * @Route("/{token}/show", name="module_show")
     */
    public function showAction(Request $request, Module $module)
    {
        $this->checkAccess($module);

        $module->viewMode = true;

        return $this->render($request->isXmlHttpRequest() ? 'module.show_content' : 'module.show', [
            'module' => $module
        ]);
    }

    /**
     * @Route("/{id}/preview", name="module_preview")
     */
    public function previewAction(Request $request, Module $module)
    {
        return $this->showAction($request, $module);
    }

    /**
     * @Route("/{token}/copy", name="module_copy")
     */
    public function copyAction(Module $module)
    {
        $this->checkAccess($module);

        $account = $this->getCurrentAccount();
        $helper = $this->getModuleHelper();

        $copy = $helper->cloneToAccount($module, $account);

        return $this->jsonResponse([
            'component' => [
                'id' => $copy->getId(),
                'token' => $copy->getToken()
            ]
        ]);
    }
}
