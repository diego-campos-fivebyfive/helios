<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Variety;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VarietyController extends FOSRestController
{
    public function postVarietyAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $varietyManager = $this->get('variety_manager');

        /** @var StringBox $stringbox */
        $variety = $varietyManager->create();
        $variety
            ->setCode($data['code'])
            ->setDescription($data['description']);
        try {
            $varietyManager->save($variety);
            $status = Response::HTTP_CREATED;
            $data = [
                'id' => $variety->getId(),
                'code' => $variety->getCode(),
                'description' => $variety->getDescription()
            ];
        }
        catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create stringbox';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getVarietyAction(Variety $id)
    {
        $variety = $id;

        $data = [
            'id' => $variety->getId(),
            'code' => $variety->getCode(),
            'description' => $variety->getDescription(),
            'type' => $variety->getType(),
            'subtype' => $variety->getSubType(),
            'maker' => $variety->getMaker()
        ];

        $view = View::create($data);

        return $this->handleView($view);
    }
}