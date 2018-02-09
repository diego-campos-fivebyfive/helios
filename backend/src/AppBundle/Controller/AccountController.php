<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/v1/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/available", name="available_accounts")
     *
     * @Method("get")
     */
    public function accountsAction()
    {
        $qb = $this->manager('account')->createQueryBuilder();

        $qb->select('a.id, a.firstname as name')
            ->from(Customer::class, 'a')
            ->where('a.status = :status')
            ->andWhere('a.context = :context')
            ->andWhere('a.email <> :email_sices')
            ->setParameters([
                'status' => AccountInterface::ACTIVATED,
                'context' => BusinessInterface::CONTEXT_ACCOUNT,
                'email_sices' => 'servidor@sicesbrasil.com.br'
            ])
            ->groupBy('a.id');

        return $this->json($qb->getQuery()->getResult(), Response::HTTP_OK);
    }
}