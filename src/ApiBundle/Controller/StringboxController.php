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
        $stringbox  ->setCode($data['code'])
                    ->setDescription($data['description']);
        try {
            $stringboxManager->save($stringbox);
            $status = Response::HTTP_CREATED;
            $data = [
                'id' => $stringbox->getId(),
                'code' => $stringbox->getCode(),
                'description' => $stringbox->getDescription()
            ];
        } catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create stringbox';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getStringboxAction(StringBox $id)
    {
        $stringbox = $id;

        $data = [
            'id' => $stringbox->getId(),
            'code' => $stringbox->getCode(),
            'description' => $stringbox->getDescription(),
            'inputs' => $stringbox->getInputs(),
            'outputs' => $stringbox->getOutputs(),
            'fuses' => $stringbox->getFuses(),
            'maker' => $stringbox->getMaker()
        ];

        $view = View::create($data);

        return $this->handleView($view);
    }
}