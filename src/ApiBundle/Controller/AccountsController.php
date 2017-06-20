<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Customer;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AccountsController extends FOSRestController
{
    public function getAccountsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();///->getRepository(Customer::class);

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('c.firstname, c.lastname, c.email, c.mobile, c.phone, c.website, c.document, c.createdAt')->from(Customer::class, 'c');
        $qb->where('c.context = :context');
        $qb->setParameters([
            'context' => Customer::CONTEXT_ACCOUNT
        ]);

        $accounts = $qb->getQuery()->getArrayResult();

        $view = View::create($accounts);

        return $this->handleView($view);

        foreach($accounts as $account){
            foreach ($account as $property => $value){
                if($value instanceof \DateTime){
                    $account[$property] = $value->format('Y-m-dTH:i:s');
                }
            }
            $accounts[] = $account;
        }

        /** @var \JMS\Serializer\Serializer $serializer */
        $serializer = $this->get('serializer');

        //dump($serializer->serialize($accounts,'json')); die;
        return new JsonResponse($accounts);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate($accounts);

        dump($pagination);

        //$manager = $this->get('account_manager');
        ///$accounts = $manager->
        //dump($paginator);
        die;

        $accounts = $this->get('app.customer_manager')->findBy([
            'context' => 'account'
        ]);



        //dump($serializer->serialize($accounts, 'json')); die;

        return $this->handleView($this->view($accounts));
    }

    public function postAccountAction()
    {
        /*return $this->json([
            'info' => 'success'
        ]);*/
    }
}
