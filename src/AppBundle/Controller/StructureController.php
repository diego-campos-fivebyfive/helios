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
use AppBundle\Entity\Component\Structure;
use AppBundle\Form\Component\StructureType;

/**
 * Class StructureController
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Route("structure")
 *
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Structures", route={"name"="structure_index"})
 */
class StructureController extends ComponentController
{
    /**
     * @Route("/", name="structure_index")
     */
    public function indexAction(Request $request)
    {
        $data = $this->prepareIndexData();

        return $this->render('component.index', $data);
    }

    /**
     * @Route("/create", name="structure_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->getStructureManager();

        /** @var Structure $structure */
        $structure = $manager->create();
        $structure->toViewMode();

        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->saveComponent($structure, $request);

        }

        return $this->render("structure.form", [
            'form' => $form->createView(),
            'structure' => $structure
        ]);
    }

    /**
     * @Breadcrumb("{structure.model}")
     * @Route("/{token}/update", name="structure_update")
     * @Method({"get","post"})
     */
    public function updateAction(Request $request, Structure $structure)
    {
        $this->checkAccess($structure);

        $structure->toViewMode();

        //$manager = $this->getStructureManager();
        $form = $this->createForm(StructureType::class, $structure, [
            'is_validation' => $this->getUser()->isAdmin()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return $this->saveComponent($structure, $request);
        }

        if($request->isMethod('get')) {
            $this->store('referer', $request->server->get('HTTP_REFERER'));
        }

        return $this->render("structure.form", [
            'form' => $form->createView(),
            'structure' => $structure
        ]);
    }

    /**
     * @Route("/{token}/delete", name="structure_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, Structure $structure)
    {
        $this->checkAccess($structure);

        $usages = $this->getDoctrine()
            ->getManager()
            ->getRepository(KitComponent::class)
            ->findBy(['structure' => $structure]);

        if(0 != $count = count($usages)){
            return $this->jsonResponse([
                'error' => sprintf('Esta estrutura está sendo utilizada em %s kits', $count)
            ], Response::HTTP_IM_USED);
        }

        $this->getStructureManager()->delete($structure);

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Breadcrumb("{structure.model}")
     * @Route("/{token}/show", name="structure_show")
     */
    public function showAction(Request $request, Structure $structure)
    {
        $this->checkAccess($structure);

        $structure->toViewMode();

        return $this->render($request->isXmlHttpRequest() ? 'structure.show_content' : 'structure.show', [
            'structure' => $structure
        ]);
    }

    /**
     * @Route("/{id}/preview", name="structure_preview")
     */
    public function previewAction(Request $request, Structure $structure)
    {
        return $this->showAction($request, $structure);
    }

    /**
     * @Route("/{token}/copy", name="structure_copy")
     */
    public function copyAction(Request $request, Structure $structure)
    {
        $this->checkAccess($structure);

        $account = $this->getCurrentAccount();
        $helper = $this->getStructureHelper();

        $copy = $helper->cloneToAccount($structure, $account);

        return $this->jsonResponse([
            'component' => [
                'id' => $copy->getId(),
                'token' => $copy->getToken()
            ]
        ]);
    }
}
