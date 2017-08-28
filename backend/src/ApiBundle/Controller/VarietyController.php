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
            ->setDescription($data['description'])
            ->setAvailable($data['available'])
            ->setStatus(true);
        try {
            $varietyManager->save($variety);
            $status = Response::HTTP_CREATED;
            $data = $this->get('api_formatter')->format($variety, ['maker' => 'id']);
        }
        catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create stringbox';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getVarietyAction(Variety $variety)
    {
        $data = $this->get('api_formatter')->format($variety, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }
}