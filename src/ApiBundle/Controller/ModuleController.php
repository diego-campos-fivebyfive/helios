<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Customer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return JsonResponse::create($module, 200);
    }

    public function getModulesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('m')
            ->from(Module::class, 'm')
            ->where('m.id = :id')
            ->setParameters([
                'id' => $id
            ]);
        $query = $qb->getQuery();

        $modules = $query->getArrayResult();

        $response = new Response(json_encode($modules));
        $response->headers->set('module', 'aplication/json');

        return $response;
    }
}
