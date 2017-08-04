<?php

namespace AppBundle\Controller\Component;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\StringBox;
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
 * @Security("has_role('ROLE_OWNER')")
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
       /* $em = $this->getDoctrine()->getManager();

        $stringBoxes = $em->getRepository('AppBundle:Component\StringBox')->findAll();*/

        $manager = $this->manager('stringbox');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('c')
            ->from(sprintf('AppBundle\Entity\Component\StringBox', ucfirst('stringbox')), 'c');

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 20
        );

        return $this->render('Stringbox.index', array(
            //'stringBoxes' => $stringBoxes,
            'pagination' => $pagination,
            'query' => array_merge([
                'display' => 'grid',
                'strict' => 0
            ], $request->query->all())
        ));
    }

   /* /**
     * @param StringboxInterface
     * @param Request $request
     * @return RedirectResponse
     */
    /* private function saveStringbox($stringbox, Request $request)
     {
         $manager = $this->manager($type);

         $manager->save($stringbox);

         $uploadDir = $this->getComponentsDir();

         foreach ($request->files->all() as $field => $uploadedFile) {

             if ($uploadedFile instanceof UploadedFile) {

                 $getter = 'get' . ucfirst($field);
                 $setter = 'set' . ucfirst($field);

                 $ext = $uploadedFile->getClientOriginalExtension();
                 $currentFile = $stringbox->$getter();

                 $format = 'pdf' == $ext ? '%s_%s.%s' : '%s_%s_thumb.%s';

                 $filename = sprintf($format, $type, $stringbox->getId(), $ext);

                 if ($currentFile) {
                     $currentFilePath = $uploadDir . $currentFile;
                     if (file_exists($currentFilePath)) {
                         unlink($currentFilePath);
                     }
                 }

                 $uploadedFile->move($uploadDir, $filename);

                 $stringbox->$setter($filename);
             }
         }

         $manager->save($stringbox);

         $this->setNotice('Componente atualizado com sucesso!');

         if (null == $url = $this->restore('referer')) {
             $url = $this->generateUrl('components', ['type' => $type]);
         }

         return $this->redirect($url);
     }
     */
  /*  /**
     * Creates a new stringBox entity.
     *
     * @Route("/new", name="stringbox_new")
     * @Method({"GET", "POST"})
     */
   /* public function newAction(Request $request)
    {
        $stringBox = new Stringbox();
        $form = $this->createForm('AppBundle\Form\Component\StringBoxType', $stringBox);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stringBox);
            $em->flush($stringBox);

            return $this->redirectToRoute('stringbox_show', array('id' => $stringBox->getId()));
        }

        return $this->render('Stringbox.new', array(
            'stringBox' => $stringBox,
            'form' => $form->createView(),
        ));
    }*/

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
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Route("/{id}/edit", name="stringbox_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, StringBox $stringBox)
    {
        $deleteForm = $this->createDeleteForm($stringBox);
        $editForm = $this->createForm('AppBundle\Form\Component\StringBoxType', $stringBox);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($stringBox->getStatus()) {
                $this->get('notifier')->notify([
                    'callback' => 'product_validate',
                    'body' => [
                        'id' => $stringBox->getId(),
                        'family' => 'stringbox'
                    ]
                ]);
            }

            return $this->redirectToRoute('stringbox_edit', array('id' => $stringBox->getId()));
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
     * @Security("has_role('ROLE_ADMIN')")
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
