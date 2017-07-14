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
    public function getModuleAction(Request $request)
    {
        return $this->json([
            'info' => 'success'
        ]);
    }

    public function postAccountAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $moduleManager = $this->get('module_manager');

        /** @var Module $module */
        $module = $moduleManager->create();
        $module ->setCode($data['code'])
                ->setModel($data['model']);
        $moduleManager->save($module);
    }
}
