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
use AppBundle\Entity\Component\Structure;
use AppBundle\Form\Component\StructureType;
use AppBundle\Service\Notifier\Notifier;

/**
 * Class StructureController
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Route("structure")
 *
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Estruturas", route={"name"="structure_index"})
 */
class StructureController extends AbstractController
{
    /**
     * @Route("/", name="structure_index")
     *
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('structure');
        $paginator = $this->getPaginator();

        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('s')->from(Structure::class, 's');

        if(!$this->user()->isAdmin()) {
            $qb->where('s.status = :status');
            $qb->setParameters([
                'status' => 1
            ]);
        }

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 10,
            ['distinct' => false]
        );

        return $this->render('structure.index', array(
            'pagination' => $pagination,
            'query' => array_merge([
                'display' => 'grid',
                'strict' => 0
            ], $request->query->all())
        ));
    }

    /**
     * @Route("/create", name="structure_create")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('structure');

        /** @var Structure $structure */
        $structure = $manager->create();

        $form = $this->createForm(StructureType::class, $structure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($structure);

            $this->setNotice('Estrutura cadastrada com sucesso!');

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
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateAction(Request $request, Structure $structure)
    {
        $manager = $this->manager('structure');

        $form = $this->createForm(StructureType::class, $structure, [
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('component_file_handler')->upload($structure, $request->files);

            $manager->save($structure);

            $message = 'Estrutura atualizada com sucesso!';

            if ($structure->isPublished()) {
                $this->get('notifier')->notify([
                    'Evento' => '302.3',
                    'Callback' => 'product',
                    'Id' => $structure->getId()
                ]);
                $message .= 'Publicação executada.';
            }

            $this->setNotice($message);

            return $this->redirectToRoute('structure_index');
        }

        return $this->render("structure.form", [
            'form' => $form->createView(),
            'structure' => $structure
        ]);
    }

    /**
     * @Route("/{id}/delete", name="structure_delete")
     * @Method("delete")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Structure $structure)
    {
        // TODO - Check project reference

        //$this->manager('structure')->delete($structure);

        return $this->json([], Response::HTTP_OK);

    }

    /**
     * @Breadcrumb("{structure.description}")
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
