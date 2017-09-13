<?php

namespace ApiBundle\Controller;

use ApiBundle\Form\ModuleType;
use ApiBundle\Handler\RequestHandler;
use AppBundle\Entity\Component\Module;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ModuleController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getModulesAction(Request $request)
    {
        $data = $this->get('api_handler')->handleRequest($request, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postModulesAction(Request $request)
    {
        $manager = $this->manager('module');

        $module = $manager->create();

        return $this->applyRequest($request, $module);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="module"})
     */
    public function getModuleAction(Module $module)
    {
        $view = View::create($module);

        return $this->handleView($view);
    }

    /**
     * @ParamConverter(converter="component_converter", options={"type"="module"})
     */
    public function putModuleAction(Request $request, Module $module)
    {
        return $this->applyRequest($request, $module);
    }

    /**
     * @param Request $request
     * @param Module $module
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function applyRequest(Request $request, Module $module)
    {
        $manager = $this->manager('module');

        $form = $this->form(ModuleType::class, $module);

        $handler = RequestHandler::create($request, $form);

        if($handler->handle()){
            $manager->save($module);
        }

        $view = $handler->view();

        return $this->handleView($view);
    }
}
