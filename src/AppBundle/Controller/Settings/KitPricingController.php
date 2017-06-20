<?php

namespace AppBundle\Controller\Settings;

use AppBundle\Controller\AbstractController;
use AppBundle\Form\Settings\KitPricingType;
use AppBundle\Model\KitPricing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/kit/pricing")
 */
class KitPricingController extends AbstractController
{
    /**
     * @Route("/", name="kit_pricing")
     */
    public function indexAction()
    {
        $manager = $this->getPricingManager();

        $parameters = $manager->findAll();

        $this->clearTemplateCache('kit.pricing_parameters');

        return $this->render('kit.pricing_parameters', [
            'parameters' => $parameters
        ]);
    }

    /**
     * @Route("/create", name="kit_pricing_create")
     */
    public function createAction(Request $request)
    {
        $manager = $this->getPricingManager();

        $pricing = $manager->create();

        $form = $this->createForm(KitPricingType::class, $pricing);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            if($manager->save($pricing)) {
                return $this->jsonResponse([], Response::HTTP_CREATED);
            }

            return $this->jsonResponse([
                'limits' => $manager->getLimits()
            ], Response::HTTP_CONFLICT);
        }

        return $this->render('kit.pricing_form', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/update", name="kit_pricing_update")
     */
    public function updateAction(Request $request, $id)
    {
        $manager = $this->getPricingManager();

        $pricing = $manager->find($id);

        $form = $this->createForm(KitPricingType::class, $pricing);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($manager->save($pricing)){
                return $this->jsonResponse([], Response::HTTP_OK);
            }

            return $this->jsonResponse([
                'limits' => $manager->getLimits()
            ], Response::HTTP_CONFLICT);
        }

        return $this->jsonResponse([], Response::HTTP_IM_USED);
    }

    /**
     * @Route("/{id}/delete", name="kit_pricing_delete")
     * @Method("delete")
     */
    public function deleteAction($id)
    {
        $manager = $this->getPricingManager();

        $pricing = $manager->find($id);
        
        $manager->delete($pricing);
        
        return $this->jsonResponse([], Response::HTTP_ACCEPTED);
    }

    /**
     * @return \AppBundle\Entity\Component\PricingManager
     */
    private function getPricingManager()
    {
        return $this->get('app.kit_pricing_manager');
    }
}
