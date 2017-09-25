<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Service\Component\ComponentFileHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * Stringbox controller.
 *
 * @Route("stringbox")
 * @Breadcrumb("String Box")
 *
 */
class StringBoxController extends AbstractController
{
    /**
     * Lists all stringBox entities.
     *
     * @Route("/", name="stringbox_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('stringbox');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('s')
            ->from(StringBox::class, 's')
            ->leftJoin('s.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('s.description', 'asc');

        if (!$this->user()->isAdmin()) {
            $qb->where('s.status = :status');
            $qb->andWhere('s.available = :available');
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

        return $this->render('Stringbox.index', array(
            'pagination' => $pagination,
            'query' => array_merge([
                'display' => 'grid',
                'strict' => 0
            ], $request->query->all())
        ));
    }

    /**
     * Finds and displays a stringBox entity.
     *
     * @Route("/{id}", name="stringbox_show")
     * @Method("GET")
     */
    public function showAction(StringBox $stringBox)
    {
        $deleteForm = $this->createDeleteForm($stringBox);

        return $this->render('Stringbox.show_content', array(
            'stringBox' => $stringBox,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing stringBox entity.
     *
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Route("/{id}/update", name="stringbox_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, StringBox $stringBox)
    {
        $deleteForm = $this->createDeleteForm($stringBox);
        $editForm = $this->createForm('AppBundle\Form\Component\StringBoxType', $stringBox);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->get('component_file_handler')->upload($stringBox, $request->files);

            $this->manager('string_box')->save($stringBox);

            $message = 'StringBox atualizado com sucesso. ';

            if ($stringBox->isPublished()) {

                $this->get('notifier')->notify([
                    'Evento' => '302.3',
                    'Callback' => 'product',
                    'Code' => $stringBox->getCode()
                ]);

                $message .= 'Publicação executada.';
            }

            $this->setNotice($message);

            return $this->redirectToRoute('stringbox_index');
        }

        return $this->render('Stringbox.edit', array(
            'stringBox' => $stringBox,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a stringBox entity.
     *
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Route("/{id}", name="stringbox_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, StringBox $stringBox)
    {
        $form = $this->createDeleteForm($stringBox);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stringBox);
            $em->flush($stringBox);
        }

        return $this->redirectToRoute('stringbox_index');
    }

    /**
     * Creates a form to delete a stringBox entity.
     *
     * @param StringBox $stringBox The stringBox entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(StringBox $stringBox)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('stringbox_delete', array('id' => $stringBox->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
