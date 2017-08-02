<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Customer;
use Doctrine\Common\Cache\VoidCache;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StructureController extends FOSRestController
{
    public function postStructuresAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $structureManager = $this->get('structure_manager');

        /** @var Structure $structure */
        $structure = $structureManager->create();
        $structure
            ->setCode($data['code'])
            ->getDescription($data['description']);

        try {
            $structureManager->save($structure);
            $status = Response::HTTP_CREATED;
            $data = [
                'id' => $structure->getId(),
                'code' => $structure->getCode(),
                'model' => $structure->getDescription()
            ];
        }catch (\Exception $exception){
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create Structure';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getStructuresAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('s')
            ->from(Structure::class, 's')
            ->where('s.id = :id')
            ->setParameters([
                'id' => $id
            ]);
        $query = $qb->getQuery();

        $structures = $query->getArrayResult();

        $response = new Response(json_encode($structures));
        $response->headers->set('structure', 'aplication/json');

        return $response;
    }
}
