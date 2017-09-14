<?php

namespace ApiBundle\Controller;

use ApiBundle\Form\InverterType;
use ApiBundle\Handler\RequestHandler;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Manager\InverterManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\Version;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class InverterController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getInvertersAction(Request $request)
    {
        $data = $this->get('api_handler')->handleRequest($request, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function postInvertersAction(Request $request)
    {
        $manager = $this->manager('inverter');

        $inverter = $manager->create();

        return $this->applyRequest($request, $inverter);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="inverter"})
     */
    public function getInverterAction(Inverter $inverter)
    {
        $view = View::create($inverter);

        return $this->handleView($view);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="inverter"})
     */
    public function putInverterAction(Request $request, Inverter $inverter)
    {
        return $this->applyRequest($request, $inverter);
    }

    /**
     * @param Request $request
     * @param Inverter $inverter
     * @return Response
     */
    private function applyRequest(Request $request, Inverter $inverter)
    {
        $manager = $this->manager('inverter');

        $form = $this->form(InverterType::class, $inverter);

        $handler = RequestHandler::create($request, $form);

        if ($handler->handle()) {
            $manager->save($inverter);
        }

        $view = $handler->view();

        return $this->handleView($view);
    }
}
