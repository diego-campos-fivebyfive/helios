<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Module;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
            ->setModel($data['model'])
            ->setAvailable($data['available'])
            ->setStatus(false);

        try {
            $moduleManager->save($module);
            $status = Response::HTTP_CREATED;
            $data = $this->get('api_formatter')->format($module, ['maker' => 'id']);
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

    /**
     * @Route("/{code}")
     * @ParamConverter("code", class="AppBundle:Component\Module", options={"mapping":{"code" : "code"}})
     */
    public function putModuleAction(Request $request, Module $code)
    {
        $data = json_decode($request->getContent(), true);

        $moduleManager = $this->get('module_manager');
        $code->setPromotional($data['promotional']);

        try {
            $moduleManager->save($code);
            $status = Response::HTTP_CREATED;
            $data = $this->get('api_formatter')->format($code, ['maker' => 'id']);
        }catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not update Module';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }
}
