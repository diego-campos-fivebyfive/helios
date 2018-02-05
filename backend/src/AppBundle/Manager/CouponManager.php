<?php

namespace AppBundle\Manager;


use AppBundle\Entity\AccountInterface;

class CouponManager extends AbstractManager
{
    /**
     * @param AccountInterface $account
     * @return array
     */
    public function findByAccount(AccountInterface $account)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('c')
            ->where('c.account = :account')
            ->setParameter('account', $account);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $target
     * @return array
     */
    public function findByTarget($target)
    {
        $qb = $this->createQueryBuilder();

        $qb->select('c')
            ->where('c.target = :target')
            ->setParameter('target', $target);

        return $qb->getQuery()->getResult();
    }
}
