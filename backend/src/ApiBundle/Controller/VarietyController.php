<?php

namespace ApiBundle\Controller;

use ApiBundle\Form\VarietyType;
use ApiBundle\Handler\RequestHandler;
use AppBundle\Entity\Component\Variety;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class VarietyController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function postVarietyAction(Request $request)
    {
        $manager = $this->manager('variety');

        $variety = $manager->create();

        return $this->applyRequest($request, $variety);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="variety"})
     */
    public function getVarietyAction(Variety $variety)
    {
        $view = View::create($variety);

        return $this->handleView($view);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="variety"})
     */
    public function putVarietyAction(Request $request, Variety $variety)
    {
        return $this->applyRequest($request, $variety);
    }

    /**
     * @param Request $request
     * @param Variety $variety
     * @return Response
     */
    private function applyRequest(Request $request, Variety $variety)
    {
        $manager = $this->manager('variety');

        $form = $this->form(VarietyType::class, $variety);

        $handler = RequestHandler::create($request, $form);

        if ($handler->handle()) {
            $manager->save($variety);
        }

        $view = $handler->view();

        return $this->handleView($view);
    }
}
