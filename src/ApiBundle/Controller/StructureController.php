<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Customer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StructureController extends FOSRestController
{
    public function getStructuresAction(Request $request)
    {
        return $this->json([
            'info' => 'success'
        ]);
    }

    public function postStructuresAction()
    {
        /*return $this->json([
            'info' => 'success'
        ]);*/
    }
}
