<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Manager\InverterManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Version;
use Symfony\Component\HttpFoundation\Response;

class InverterController extends FOSRestController
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

    public function postInvertersAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $inverterManager = $this->get('inverter_manager');

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter
            ->setCode($data['code'])
            ->setModel($data['model'])
            ->setAvailable($data['available'])
            ->setStatus(false);

        try{
            $inverterManager->save($inverter);
            $status = Response::HTTP_CREATED;
            $data = $this->get('api_formatter')->format($inverter, ['maker' => 'id']);
        }
        catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create Inverter';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getInverterAction(Inverter $inverter)
    {
        $data = $this->get('api_formatter')->format($inverter, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }
}
