<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Customer;
use Doctrine\Common\Cache\VoidCache;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StructureController extends FOSRestController
{
    public function postStructuresAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $structureManager = $this->get('structure_manager');

        /** @var Structure $structure */
        $structure = $structureManager->create();
        $structure
            ->setCode($data['code'])
            ->setDescription($data['description'])
            ->setAvailable($data['available'])
            ->setStatus(false);

        try {
            $structureManager->save($structure);
            $status = Response::HTTP_CREATED;
            $data = $this->get('api_formatter')->format($structure, ['maker' => 'id']);
        }catch (\Exception $exception){
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create Structure';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getStructureAction(Structure $structure)
    {
        $data = $this->get('api_formatter')->format($structure, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }
}
