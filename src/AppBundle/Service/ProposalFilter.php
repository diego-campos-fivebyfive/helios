<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Manager\ProjectManager;
use Doctrine\ORM\Query\Expr\Join;

/**
 * This class provides filtering features for issued proposals,
 * considering monthly or annual time intervals, users and accounts
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class ProposalFilter
{
    /**
     * @var ProjectManager
     */
    private $manager;

    /**
     * @var array
     */
    private $filters = [
        'member' => null,
        'account' => null,
        'date' => null,
        'at' => 'month'
    ];

    /**
     * ProposalFilter constructor.
     * @param ProjectManager $manager
     */
    function __construct(ProjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $by
     * @return $this
     */
    public function at($at)
    {
        $this->filters['at'] = $at;

        return $this;
    }

    /**
     * @param \DateTime|null $date
     * @return $this
     */
    public function date(\DateTime $date = null)
    {
        if(!$date){
            $date = new \DateTime();
        }

        $this->filters['date'] = $date;

        return $this;
    }

    /**
     * @param MemberInterface $member
     * @return $this
     */
    public function member(MemberInterface $member)
    {
        $this->filters['member'] = $member;

        return $this;
    }

    /**
     * @param AccountInterface $account
     * @return $this
     */
    public function account(AccountInterface $account)
    {
        $this->filters['account'] = $account;

        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        //  Global QueryBuilder
        $qb = $this->manager
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('p')->from($this->manager->getClass(), 'p');

        $member = $this->filters['member'];
        $account = $this->filters['account'];
        $date = $this->filters['date'];

        //  Add Member Reference
        if($member instanceof BusinessInterface){
            $qb->join('p.member', 'm', Join::WITH, $qb->expr()->eq('m.id', $member->getId()));
        }else{
            $qb->join('p.member', 'm');
        }

        //  Add Account Reference
        if($account instanceof BusinessInterface){
            $qb->join('m.account', 'a', Join::WITH, $qb->expr()->eq('a.id', $account->getId()));
        }

        //  Add Date Reference
        if($date instanceof \DateTime){

            $start = $date->format('Y-01-01 00:00:00');
            $end = $date->format('Y-12-31 23:59:59');

            if('month' == $this->filters['at']){

                $lastDay = cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));

                $start = $date->format('Y-m-01 00:00:00');
                $end = $date->format(sprintf('Y-m-%d 23:59:59', $lastDay));
            }

            $qb->where('p.issuedAt >= :start')->andWhere('p.issuedAt <= :end');

            $qb->setParameter('start', $start);
            $qb->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();
    }
}