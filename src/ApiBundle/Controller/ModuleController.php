<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Module;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getModulesAction(Request $request)
    {
        $data = $this->get('api_handler')->handleRequest($request, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }

    public function postModulesAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $moduleManager = $this->get('module_manager');

        /** @var Module $module */
        $module = $moduleManager->create();
        $module
            ->setCode($data['code'])
            ->setModel($data['model']);

        try {
            $moduleManager->save($module);
            $status = Response::HTTP_CREATED;
            $data = [
                'id' => $module->getId(),
                'code' => $module->getCode(),
                'model' => $module->getModel()
            ];
        }
        catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create Module';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getModuleAction(Module $module)
    {
        $data = $this->get('api_formatter')->format($module, ['maker' => 'id']);

        $view = View::create($data);

        return $this->handleView($view);
    }
}
