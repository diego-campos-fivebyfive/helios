<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Structure;
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

    public function postStructuresAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $structureManager = $this->get('structure_manager');

        /** @var Structure $structure */
        $structure = $structureManager->create();
        $structure  ->setCode($data['code'])
                    ->getDescription($data['description']);
        $structureManager->save($structure);
    }
}
