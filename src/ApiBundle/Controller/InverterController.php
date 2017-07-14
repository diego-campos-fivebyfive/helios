<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Manager\InverterManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\Annotations\Route;

class InverterController extends FOSRestController
{

    public function getInvertersAction(Request $request)
    {

    }

    public function postInvertersAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $inverterManager = $this->get('inverter_manager');

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter   ->setCode($data['code'])
                    ->setModel($data['model']);
        $inverterManager->save($inverter);
    }
}
