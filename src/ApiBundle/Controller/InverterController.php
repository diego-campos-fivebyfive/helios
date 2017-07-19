<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Manager\InverterManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Version;
use Symfony\Component\HttpFoundation\Response;

class InverterController extends FOSRestController
{
    public function postInvertersAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $inverterManager = $this->get('inverter_manager');

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter   ->setCode($data['code'])
                    ->setModel($data['model']);
        $inverterManager->save($inverter);

        $view = View::create();
        return $this->handleView($view);
    }

    public function getInvertersAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('i')
            ->from(Inverter::class, 'i')
            ->where('i.id = :id')
            ->setParameters([
                'id' => $id
            ]);
        $query = $qb->getQuery();

        $inverters = $query->getArrayResult();

        $response = new Response(json_encode($inverters));
        $response->headers->set('inverter', 'aplication/json');

        return $response;
    }
}
