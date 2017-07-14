<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Customer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends FOSRestController
{
    public function postModulesAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $moduleManager = $this->get('module_manager');

        /** @var Module $module */
        $module = $moduleManager->create();
        $module ->setCode($data['code'])
                ->setModel($data['model']);
        $moduleManager->save($module);

        $view = View::create();
        return $this->handleView($view);
    }
}
