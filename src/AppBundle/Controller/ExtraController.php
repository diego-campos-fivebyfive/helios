<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Extra;
use AppBundle\Form\Component\ExtraType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("extras")
 *
 */
class ExtraController extends AbstractController
{
    /**
     * @Route("/", name="extras_index")
     * @Method("GET")
     * @Breadcrumb("Extras", route={"name"="extras_index"})
     */
    public function indexAction()
    {
        $extras = $this->manager('extra')->findAll();
        $paginator = $this->getPaginator();

        return $this->render('extra.index', array(
            'extras' => $extras,
        ));
    }

    /**
     * @Route("/create", name="extras_create")
     * @Method({"GET", "POST"})
     * @Breadcrumb("Extras")
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

            return $this->redirectToRoute('extras_index');
        }

        return $this->render('extra.form', array(
            'extra' => $extra,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/update", name="extras_update")
     * @Method({"GET", "POST"})
     * @Breadcrumb("Extras")
     */
    public function updateAction(Request $request, Extra $extra)
    {
        $editForm = $this->createForm(ExtraType::class, $extra, []);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->manager('extra')->save($extra);

            return $this->redirectToRoute('extras_index');
        }

        return $this->render('extra.form', array(
            'extra' => $extra,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="extras_delete")
     * @Method("delete")
     */
    public function deleteAction(Extra $extra)
    {
        $this->manager('extra')->delete($extra);

        return $this->json([]);
    }
}
