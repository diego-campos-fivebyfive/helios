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
use Symfony\Component\HttpFoundation\Response;

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
        $manager = $this->manager('string_box');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('s')
            ->from(StringBox::class, 's')
            ->leftJoin('s.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('s.description', 'asc');

        if (!$this->user()->isAdmin() && !$this->user()->isPlatformAdmin() && !$this->user()->isPlatformMaster()) {
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
     * Displays a form to edit an existing stringBox entity.
     *
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Route("/create", name="stringbox_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('string_box');

        /** @var StringBox $stringBox */
        $stringBox = $manager->create();

        $editForm = $this->createForm('AppBundle\Form\Component\StringBoxType', $stringBox);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $manager->save($stringBox);

            $this->get('component_file_handler')->upload($stringBox, $request->files);

            $manager->save($stringBox);

            $this->setNotice('StringBox criado com sucesso.');

            return $this->redirectToRoute('stringbox_index');
        }

        return $this->render('Stringbox.edit', array(
            'stringBox' => $stringBox,
            'form' => $editForm->createView()
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
        return $this->render('Stringbox.show_content', array(
            'stringBox' => $stringBox,
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
        $editForm = $this->createForm('AppBundle\Form\Component\StringBoxType', $stringBox);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->get('component_file_handler')->upload($stringBox, $request->files);

            $this->manager('string_box')->save($stringBox);

            $message = 'StringBox atualizado com sucesso.';

            $this->setNotice($message);

            return $this->redirectToRoute('stringbox_index');
        }

        return $this->render('Stringbox.edit', array(
            'stringBox' => $stringBox,
            'form' => $editForm->createView(),
        ));
    }

    /**
     *
     * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
     *
     * @Route("/{id}/delete/", name="stringbox_delete")
     * @Method("delete")
     */
    public function deleteAction(StringBox $stringBox)
    {
        $usageManager = $this->manager('project_string_box');

        if ($usageManager->findOneBy(['stringBox' => $stringBox->getId()])) {
            $message = 'Este Stringbox não pode ser excluído';
            $status = Response::HTTP_LOCKED;
        } else {
            try {
                $this->manager('string_box')->delete($stringBox);
                $message = 'Stringbox excluída com sucesso';
                $status = Response::HTTP_OK;
            } catch (\Exception $exception) {
                $message = 'Falha ao excluir Stringbox';
                $status = Response::HTTP_CONFLICT;
            }
        }

        return $this->json(['message' => $message], $status);
    }
}
