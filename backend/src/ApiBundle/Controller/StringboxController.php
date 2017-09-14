<?php

namespace ApiBundle\Controller;

use ApiBundle\Form\StringBoxType;
use ApiBundle\Handler\RequestHandler;
use AppBundle\Entity\Component\StringBox;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class StringboxController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStringboxesAction(Request $request)
    {
        $data = $this->get('api_handler')->handleRequest($request, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postStringboxAction(Request $request)
    {
        $manager = $this->manager('stringbox');

        $stringbox = $manager->create();

        return $this->applyRequest($request, $stringbox);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="stringBox"})
     */
    public function getStringboxAction(StringBox $stringBox)
    {
        $view = View::create($stringBox);

        return $this->handleView($view);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="stringBox"})
     */
    public function putStringboxAction(Request $request, StringBox $stringBox)
    {
        return $this->applyRequest($request, $stringBox);
    }

    /**
     * @param Request $request
     * @param StringBox $stringBox
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function applyRequest(Request $request, StringBox $stringBox)
    {
        $manager = $this->manager('stringbox');

        $form = $this->form(StringBoxType::class, $stringBox);

        $handler = RequestHandler::create($request, $form);

        if ($handler->handle()) {
            $manager->save($stringBox);
        }

        $view = $handler->view();

        return $this->handleView($view);
    }
}
