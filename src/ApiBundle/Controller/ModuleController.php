<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Customer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ModuleController extends FOSRestController
{
    public function getModuleAction(Request $request)
    {
        return $this->json([
            'info' => 'success'
        ]);
    }

    public function postAccountAction()
    {
        /*return $this->json([
            'info' => 'success'
        ]);*/
    }
}
