<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Extra;
use AppBundle\Form\Component\ExtraType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("extras")
 *
 */
class ExtraController extends AbstractController
{
    /**
     * @Route("/", name="extras_index")
     * @Method("GET")
     * @Breadcrumb("Meus Itens", route={"name"="extras_index"})
     */
    public function indexAction()
    {
        $extras = $this->manager('extra')->findAll();
        return $this->render('extra.index', array(
            'extras' => $extras
        ));
    }

    /**
     * @Route("/all", name="extras_all")
     */
    public function allAction()
    {
        $extras = $this->manager('extra')->findAll();

        $this->clearTemplateCache('extra.extras_parameters');

        return $this->render('extra.extras_parameters', [
            'extras' => $extras
        ]);
    }

    /**
     * @Route("/create", name="extras_create")
     * @Method({"GET", "POST"})
     * @Breadcrumb("Meus Itens")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('extra');
        $extra = $manager->create();

        $extra->setAccount($this->account());

        $form = $this->createForm(ExtraType::class, $extra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($extra);
            return $this->json([], Response::HTTP_CREATED);
        }

        return $this->render('extra.extra_form', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/update", name="extras_update")
     * @Method({"GET", "POST"})
     * @Breadcrumb("Meus Itens")
     */
    public function updateAction(Request $request, Extra $extra)
    {
        $editForm = $this->createForm(ExtraType::class, $extra, []);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->manager('extra')->save($extra);

            return $this->json([], Response::HTTP_OK);
        }

        return $this->json([], Response::HTTP_IM_USED);
    }

    /**
     * @Route("/{id}", name="extras_delete")
     * @Method("delete")
     */
    public function deleteAction(Extra $extra)
    {
        $this->manager('extra')->delete($extra);

        return $this->json([], Response::HTTP_ACCEPTED);
    }
}
