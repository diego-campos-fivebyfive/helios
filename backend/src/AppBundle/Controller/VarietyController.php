<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Variety;
use AppBundle\Form\Component\VarietyType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Service\Component\ComponentFileHandler;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;


/**
 * Variety controller.
 *
 * @Route("variety")
 * @Breadcrumb("Variedades")
 *
 */
class VarietyController extends AbstractController
{
    /**
     * @Route("/", name="variety_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('variety');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('v')
            ->from(Variety::class, 'v')
            ->leftJoin('v.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('v.description', 'asc');

        if (!$this->user()->isAdmin() && !$this->user()->isPlatformAdmin() && !$this->user()->isPlatformMaster()) {
            $qb->where('v.status = :status');
            $qb->andWhere('v.available = :available');
            $qb->setParameters([
                'status' => 1,
                'available' => 1
            ]);
        }

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 10
        );

        return $this->render('Variety.index', array(
            'pagination' => $pagination,
            'query' => array_merge([
                'display' => 'grid',
                'strict' => 0
            ], $request->query->all())
        ));
    }

    /**
     * @Route("/create", name="create_variety")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('variety');
        $variety = $manager->create();
        $form = $this->createForm(VarietyType::class, $variety);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($variety);

            $this->get('component_file_handler')->upload($variety, $request->files);

            $manager->save($variety);

            $this->setNotice('Variedade cadastrada com sucesso. ');

            return $this->redirectToRoute('variety_index');
        }

        return $this->render('Variety.form', array(
            'variety' => $variety,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="variety_show")
     * @Method("GET")
     */
    public function showAction(Variety $variety)
    {
        $deleteForm = $this->createDeleteForm($variety);

        return $this->render('Variety.show_content', array(
            'variety' => $variety,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Route("/{id}/update", name="variety_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Variety $variety)
    {
        $deleteForm = $this->createDeleteForm($variety);
        $editForm = $this->createForm('AppBundle\Form\Component\VarietyType', $variety);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->get('component_file_handler')->upload($variety, $request->files);

            $this->manager('variety')->save($variety);

            $message = 'Variedade atualizada com sucesso. ';

            $this->setNotice($message);

            return $this->redirectToRoute('variety_index');
        }

        return $this->render('Variety.form', array(
            'variety' => $variety,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Route("/{id}/delete/", name="variety_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Variety $variety)
    {
        $usageManager = $this->manager('Project_Variety');

        if ($usageManager->findOneBy(['variety' => $variety->getId()])) {
            $message = 'Esta variedade não pode ser excluída';
            $status = Response::HTTP_LOCKED;
        } else {
            try {
                $message = 'Variedade excluída com sucesso';
                $status = Response::HTTP_OK;
                $this->manager('variety')->delete($variety);
            } catch (\Exception $exception) {
                $message = 'Falha ao excluir variedade';
                $status = Response::HTTP_CONFLICT;
            }
        }

        return $this->json([
            'message' => $message
        ], $status);
    }

    /**
     * @param Variety $variety The variety entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Variety $variety)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('variety_delete', array('id' => $variety->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}
