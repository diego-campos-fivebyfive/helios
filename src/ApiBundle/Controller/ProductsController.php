<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Costumer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProdutcsController extends FOSRestController
{
	public function getProductsAction(Request $request)
	{
		$fields = '';

		$em = $this->getDoctrine()->getManager();///->getRepository(Customer::class);

		$qb = $em->createQueryBuilder();

		$qb->select($fields)->from(Costumer::class, 'p');
		$qb->where('p.context = :context');
		$qb->setParameters([
            'context' => Customer::CONTEXT_MODULE
        ]);

        $query = $qb->getQuery();

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('per_page', 5)
        );

        $products =  ['products' => $pagination->getItems(), 'next' => 'próxima', 'prev' => 'anterior'];

        $view = View::create($products);

        return $this->handleView($view);

         /** @var \JMS\Serializer\Serializer $serializer */
        $serializer = $this->get('serializer');

        //dump($serializer->serialize($accounts,'json')); die;
        return new JsonResponse($products);


        return $this->handleView($this->view($products));

	}

}
