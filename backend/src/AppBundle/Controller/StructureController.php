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
        $qb->select('s')
            ->from(Structure::class, 's')
            ->leftJoin('s.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('s.description', 'asc');

        if(!$this->user()->isAdmin() && !$this->user()->isPlatformAdmin() && !$this->user()->isPlatformMaster()) {
            $qb->where('s.status = :status');
            $qb->andWhere('s.available = :available');
            $qb->setParameters([
                'status' => 1,
                'available' => 1
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
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
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

            $this->get('component_file_handler')->upload($structure, $request->files);

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
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
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

            $this->setNotice('Estrutura atualizada com sucesso!');

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
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     */
    public function deleteAction(Structure $structure)
    {
        $usageManager = $this->manager('Project_Structure');

        if ($usageManager->findOneBy(['structure' => $structure->getId()])) {
            $message = 'Esta estrutura não pode ser excluída';
            $status = Response::HTTP_LOCKED;
        } else {
            try {
                $message = 'Estrutura excluída com sucesso';
                $status = Response::HTTP_OK;
                $this->manager('structure')->delete($structure);
            } catch (\Exception $exception) {
                $message = 'Falha ao excluir estrutura';
                $status = Response::HTTP_CONFLICT;
            }
        }

        return $this->json([
            'message' => $message
        ], $status);
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
