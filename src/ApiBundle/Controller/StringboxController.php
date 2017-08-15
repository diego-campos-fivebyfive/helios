<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\StringBox;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StringboxController extends FOSRestController
{
    public function postStringboxAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $stringboxManager = $this->get('string_box_manager');

        /** @var StringBox $stringbox */
        $stringbox = $stringboxManager->create();
        $stringbox
            ->setCode($data['code'])
            ->setDescription($data['description'])
            ->setAvailable($data['available']);

        try {
            $stringboxManager->save($stringbox);
            $status = Response::HTTP_CREATED;
            $data = $this->get('api_formatter')->format($stringbox, ['maker' => 'id']);
        }
        catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create stringbox';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getStringboxAction(StringBox $stringbox)
    {
        $data = $this->get('api_formatter')->format($stringbox, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }
}