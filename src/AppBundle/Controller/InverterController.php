<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\KitComponent;
use AppBundle\Entity\Component\MakerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Form\Component\InverterType;

/**
 * Class InverterController
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Route("inverter")
 *
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Inverters", route={"name"="inverter_index"})
 */
class InverterController extends ComponentController
{
    /**
     * @Route("/", name="inverter_index")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getStructureManager();
        $paginator = $this->getPaginator();

        $query = $manager->getEntityManager()->createQueryBuilder();
        $query->select('i')->from('AppBundle\Entity\Component\Inverter', 'i');

        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1), 10
        );

        return $this->render('inverter.index', array(
            'pagination' => $pagination
        ));
    }

    /**
     * @Route("/create", name="inverter_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->getInverterManager();

        /** @var Inverter $inverter */
        $inverter = $manager->create();

        $form = $this->createForm(InverterType::class, $inverter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->saveComponent($inverter, $request);

        }

        return $this->render("inverter.form", [
            'form' => $form->createView(),
            'inverter' => $inverter
        ]);
    }

    /**
     * @Breadcrumb("{inverter.model}")
     * @Route("/{id}/update", name="inverter_update")
     * @Method({"get","post"})
     */
    public function updateAction(Request $request, Inverter $inverter)
    {
        $this->checkAccess($inverter);

        //$manager = $this->getInverterManager();
        $form = $this->createForm(InverterType::class, $inverter, [
            'is_validation' => $this->getUser()->isAdmin()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->saveComponent($inverter, $request);

            /*$this->uploadComponentFiles($inverter, $request->files);

            $manager->save($inverter);

            $this->setNotice("Inversor Atualizado com sucesso !");

            if(null != $referer = $this->restore('referer')){
                return $this->redirect($referer);
            }

            return $this->redirectToRoute('inverter_index');*/
        }
        
        if($request->isMethod('get')) {
            $this->store('referer', $request->server->get('HTTP_REFERER'));
        }

        return $this->render("inverter.form", [
            'form' => $form->createView(),
            'inverter' => $inverter
        ]);
    }

    /**
     * @Route("/{id}/delete", name="inverter_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, Inverter $inverter)
    {
        $this->checkAccess($inverter);

        $usages = $this->getDoctrine()
            ->getManager()
            ->getRepository(KitComponent::class)
            ->findBy(['inverter' => $inverter]);

        if(0 != $count = count($usages)){
            return $this->jsonResponse([
                'error' => sprintf('Este inversor está sendo utilizado em %s kits', $count)
            ], Response::HTTP_IM_USED);
        }

        $this->getInverterManager()->delete($inverter);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Breadcrumb("{inverter.model}")
     * @Route("/{token}/show", name="inverter_show")
     */
    public function showAction(Request $request, Inverter $inverter)
    {
        $this->checkAccess($inverter);

        $inverter->toViewMode();

        return $this->render($request->isXmlHttpRequest() ? 'inverter.show_content' : 'inverter.show', [
            'inverter' => $inverter
        ]);
    }

    /**
     * @Route("/{id}/preview", name="inverter_preview")
     */
    public function previewAction(Request $request, Inverter $inverter)
    {
        return $this->showAction($request, $inverter);
    }

    /**
     * @Route("/{token}/copy", name="inverter_copy")
     */
    public function copyAction(Request $request, Inverter $inverter)
    {
        $this->checkAccess($inverter);

        $account = $this->getCurrentAccount();
        $helper = $this->getInverterHelper();

        $copy = $helper->cloneToAccount($inverter, $account);

        return $this->jsonResponse([
            'component' => [
                'id' => $copy->getId(),
                'token' => $copy->getToken()
            ]
        ]);
    }
}
