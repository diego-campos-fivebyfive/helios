<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Misc\AdditiveInterface;
use AdminBundle\Form\Misc\InsuranceType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Breadcrumb("Seguro")
 *
 * @Route("/insurance")
 */
class InsuranceController extends AbstractController
{
    /**
     * @Route("/", name="insurance_index")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('additive');

        return $this->render('admin/insurance/index.html.twig', [
        ]);
    }

    /**
     * @Route("/create", name="insurance_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->manager('additive');

        /** @var AdditiveInterface $insurance */
        $insurance = $manager->create();

        $form = $this->createForm(InsuranceType::class, $insurance);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->save($insurance);

            return $this->json([]);
        }

        return $this->render('admin/insurance/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/update", name="insurance_update")
     */
    public function updateAction(Request $request, Additive $insurance)
    {
        $form = $this->createForm(InsuranceType::class, $insurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager('additive')->save($insurance);

            return $this->json([]);
        }

        return $this->render('admin/insurance/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="insurance_delete")
     * @Method("delete")
     */
    public function deleteAction(Additive $insurance)
    {
        $this->manager('additive')->delete($insurance);

        return $this->json([]);
    }
}
