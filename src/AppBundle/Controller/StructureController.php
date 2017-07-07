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

        $manager = $this->getStructureManager();
        $paginator = $this->getPaginator();

        $query = $manager->getEntityManager()->createQueryBuilder();
        $query->select('s')->from('AppBundle\Entity\Component\Structure', 's');

        $pagination = $paginator->paginate(
            $query->getQuery(), $request->query->getInt('page', 1), 10
        );

        return $this->render('structure.index', array(
            'pagination' => $pagination,
            'display' => $request->get('display', 'grid')
        ));
    }

    /**
     * @Route("/create", name="structure_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->getStructureManager();

        /** @var Structure $structure */
        $structure = $manager->create();

        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($structure);

            return $this->redirectToRoute('structure_index');
        }

        return $this->render("structure.form", [
            'form' => $form->createView(),
            'structure' => $structure
        ]);
    }

    /**
     * @Breadcrumb("Edit")
     * @Route("/{id}/update", name="structure_update")
     * @Method({"GET","POST"})
     */
    public function updateAction(Request $request, Structure $structure)
    {

        $manager = $this->getStructureManager();

        $form = $this->createForm(StructureType::class, $structure, [
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($structure);

            return $this->redirectToRoute('structure_index');
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
     * @Route("/{id}/delete", name="structure_delete")
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
     * @Route("/{id}/show", name="structure_show")
     */
    public function showAction(Request $request, Structure $structure)
    {

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
}
