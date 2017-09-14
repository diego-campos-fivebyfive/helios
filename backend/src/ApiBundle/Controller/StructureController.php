<?php

namespace ApiBundle\Controller;

use ApiBundle\Form\StructureType;
use ApiBundle\Handler\RequestHandler;
use AppBundle\Entity\Component\Structure;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class StructureController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStructuresAction(Request $request)
    {
        $data = $this->get('api_handler')->handleRequest($request, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postStructuresAction(Request $request)
    {
        $manager = $this->manager('structure');

        $structure = $manager->create();

        return $this->applyRequest($request, $structure);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="structure"})
     */
    public function getStructureAction(Structure $structure)
    {
        $view = View::create($structure);

        return $this->handleView($view);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="structure"})
     */
    public function putStructureAction(Request $request, Structure $structure)
    {
        return $this->applyRequest($request, $structure);
    }

    /**
     * @param Request $request
     * @param Structure $structure
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function applyRequest(Request $request, Structure $structure)
    {
        $manager = $this->manager('structure');

        $form = $this->form(StructureType::class, $structure);

        $handler = RequestHandler::create($request, $form);

        if ($handler->handle()) {
            $manager->save($structure);
        }

        $view = $handler->view();

        return $this->handleView($view);
    }
}
