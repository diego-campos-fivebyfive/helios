<?php

namespace AppBundle\Service;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Task;
use AppBundle\Entity\TaskManager;
use Doctrine\ORM\QueryBuilder;

class TaskFilter
{
    /**
     * @var TaskManager
     */
    private $taskManager;

    /**
     * @var QueryBuilder
     */
    private $qb;

    function __construct(TaskManager $taskManager)
    {
        $this->taskManager = $taskManager;
        $this->createQueryBuilder();
    }

    /**
     * @return array
     */
    public function get()
    {
        $result = $this->qb->getQuery()->getResult();

        $this->createQueryBuilder();

        return $result;
    }

    /**
     * @param BusinessInterface $author
     * @return $this
     */
    public function author(BusinessInterface $author)
    {
        $this->qb
            ->andWhere('t.author = :author')
            ->setParameter('author', $author)
        ;

        return $this;
    }

    /**
     * @param BusinessInterface $contact
     * @return $this
     */
    public function contact(BusinessInterface $contact)
    {
        $this->qb
            ->andWhere('t.contact = :contact')
            ->setParameter('contact', $contact->getId())
        ;

        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function type($type)
    {
       $this->typify($type);

        $this->qb
            ->andWhere('t.type = :type')
            ->setParameter('type', $type);

        return $this;
    }

    /**
     * @param $types
     * @return $this|TaskFilter
     */
    public function types($types)
    {
        $types = is_array($types) ? $types : func_get_args();
        $types = array_values($types);

        foreach($types as $type){
            $this->typify($type);
        }

        if(1==count($types))
            return $this->type($types[0]);

        $this->qb->andWhere($this->qb->expr()->in('t.type', $types));

        return $this;
    }

    /**
     * @param $status
     * @return $this
     */
    public function status($status)
    {
        $this->statusSet($status);

        $this->qb
            ->andWhere('t.status = :status')
            ->setParameter('status', $status);

        return $this;
    }

    /**
     * @param BusinessInterface $member
     * @return $this
     */
    public function member(BusinessInterface $member)
    {
        $this->qb
            ->join('t.members', 'm', 'WITH', 'm.id = :member')
            ->setParameter('member', $member)
        ;

        return $this;
    }

    /**
     * @param $members
     * @return $this|TaskFilter
     */
    public function members($members)
    {
        $members = func_get_args();

        foreach($members as $member){
            if(!$member instanceof BusinessInterface || !$member->isMember()){
                throw new \InvalidArgumentException('Invalid member context');
            }
        }

        if(1 == count($members)) {
            return $this->member($members[0]);
        }

        $ids = array_map(function(BusinessInterface $member){
            return $member->getId();
        }, $members);

        $this->qb->join('t.members', 'm', 'WITH', $this->qb->expr()->in('m.id', $ids));

        return $this;
    }

    /**
     * @param null $date
     * @return $this
     */
    public function week($date = null)
    {
        if(!$date) $date = new \DateTime;

        $week = $date instanceof \DateTime ? $date->format('W') : (int) $date;
        $year = $date instanceof \DateTime ? $date->format('Y') : (new \DateTime())->format('Y');

        $start = (new \DateTime())->setISODate($year, $week, 0);
        $end = (new \DateTime())->setISODate($year, $week, 6);

        $this->addDateTimeCriteria($start, $end);

        return $this;
    }

    /**
     * @param null $date
     * @return $this
     */
    public function date($date = null)
    {
        if(!$date || !$date instanceof \DateTime) $date = new \DateTime;

        $this->addDateTimeCriteria($date, $date);

        return $this;
    }

    /**
     * @param $start
     * @param $end
     * @return $this
     */
    public function interval($start, $end)
    {
        if(!$start instanceof \DateTime){
            $start = new \DateTime($start);
        }

        if(!$end instanceof \DateTime){
            $end = new \DateTime($end);
        }

        $this->addDateTimeCriteria($start, $end);

        return $this;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function getQuery()
    {
        return $this->qb->getQuery();
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     */
    private function addDateTimeCriteria(\DateTime $start, \DateTime $end)
    {
        $this->qb
            ->andWhere(
                $this->qb->expr()->andX(
                    't.startAt >= :start',
                    't.endAt <= :end'
                ))
            ->setParameter('start', $start->format('Y-m-d 00:00:00'))
            ->setParameter('end', $end->format('Y-m-d 23:59:59'));

        return $this;
    }

    /**
     * Define a query builder
     */
    private function createQueryBuilder()
    {
        $this->qb =
            $this->taskManager
                ->getEntityManager()
                ->createQueryBuilder()
                ->select('t')
                ->from($this->taskManager->getClass(), 't')
                ->orderBy('t.startAt', 'asc')
        ;
    }

    /**
     * @param $type
     */
    private function typify(&$type)
    {
        if (!array_key_exists($type, Task::getTypes())
            && !in_array($type, Task::getTypes())) {
            throw new \InvalidArgumentException('Invalid [type] definition');
        }
        if(is_string($type)) {
            $type = array_search($type, Task::getTypes());
        }
    }

    /**
     * @param $status
     */
    private function statusSet(&$status)
    {
        if (!array_key_exists($status, Task::getStatuses())
            && !in_array($status, Task::getStatuses())) {
            throw new \InvalidArgumentException('Invalid [status] definition');
        }
        if(is_string($status)) {
            $status = array_search($status, Task::getStatuses());
        }
    }
}