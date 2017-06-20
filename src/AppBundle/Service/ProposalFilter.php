<?php

namespace AppBundle\Service;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Financial\ProjectFinancialManager;
use AppBundle\Entity\Project\ProjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr\Join;

class ProposalFilter
{
    /**
     * @var ProjectFinancialManager
     */
    private $manager;

    private $defaults = [
        'at' => ['day', 'month', 'year']
    ];

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
     * @param ProjectFinancialManager $manager
     */
    function __construct(ProjectFinancialManager $manager)
    {
        $this->manager = $manager;
        //$this->filters['date'] = new \DateTime;
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
     * @param BusinessInterface $member
     * @return $this
     */
    public function member(BusinessInterface $member)
    {
        if(!$member->isMember()){
            $this->unsupportedContextException();
        }

        $this->filters['member'] = $member;

        return $this;
    }

    /**
     * @param BusinessInterface $account
     * @return $this
     */
    public function account(BusinessInterface $account)
    {
        if(!$account->isAccount()){
            $this->unsupportedContextException();
        }

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
            ->select('f')->from($this->manager->getClass(), 'f')
            ->join('f.proposal', 'd')
            ->join('f.project', 'p');

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

            $qb->where('f.createdAt >= :start')->andWhere('f.createdAt <= :end');

            $qb->setParameter('start', $start);
            $qb->setParameter('end', $end);
        }
        
        return $qb->getQuery()->getResult();
    }

    public function groupBy($group)
    {

    }

    private function unsupportedContextException()
    {
        throw new \InvalidArgumentException('Unsupported context');
    }
}