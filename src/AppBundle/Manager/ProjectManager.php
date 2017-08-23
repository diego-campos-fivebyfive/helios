<?php

namespace AppBundle\Manager;

use AppBundle\Entity\AccountInterface;
use Doctrine\ORM\Query\Expr\Join;

class ProjectManager extends AbstractManager
{
    /**
     * @param AccountInterface $account
     * @return array
     */
    public function findByAccount(AccountInterface $account)
    {
        $qb = $this->createQueryBuilder();

        $qb->join('p.member', 'm')
            ->join('m.account', 'a', Join::WITH, $qb->expr()->eq('a.id', $account->getId()))
        ;

        return $qb->getQuery()->getResult();
    }
}