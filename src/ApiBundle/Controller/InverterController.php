<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Inverter;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Version;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\Post;

class InverterController extends FOSRestController
{

    public function getInvertersAction(Request $request)
    {
        return $this->json([
            'info' => 'success'
        ]);
    }

    public function postInvertersAction()
    {
        /*return $this->json([
            'info' => 'success'
        ]);*/
    }
}
