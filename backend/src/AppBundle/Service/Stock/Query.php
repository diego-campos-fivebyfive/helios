<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Entity\Stock\Transaction;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query as DoctrineQuery;
use Knp\Component\Pager\Pagination\AbstractPagination as Pagination;

class Query
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Query constructor.
     * @param Provider $provider
     */
    function __construct(Provider $provider)
    {
        $this->provider = $provider;

        $this->initialize();
    }

    /**
     * @param $family
     * @param $identity
     * @return $this
     */
    public function product($family, $identity)
    {
        $this->qb->andWhere('t.family = :family');
        $this->qb->andWhere('t.identity = :identity');
        $this->parameters['family'] = $family;
        $this->parameters['identity'] = $identity;

        return $this;
    }

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     * @return Query
     */
    public function between(\DateTime $startAt, \DateTime $endAt)
    {
        $this->qb->andWhere('t.createdAt >= :startAt');
        $this->qb->andWhere('t.createdAt <= :endAt');

        $this->parameters['startAt'] = $startAt->format('Y-m-d 00:00:00');
        $this->parameters['endAt'] = $endAt->format('Y-m-d 23:59:59');

        return $this;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function query()
    {
        return $this->get('query');
    }

    /**
     * @return QueryBuilder
     */
    public function qb()
    {
        return $this->get('qb');
    }

    /**
     * @return array
     */
    public function result()
    {
        return $this->get('result');
    }

    public function sql()
    {
        return $this->get('sql');
    }

    /**
     * @return Pagination
     */
    public function paginate($page)
    {
        $query = $this->get('query');
        return $this->provider->get('knp_paginator')->paginate($query, $page, 10);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->get('count');
    }

    /**
     * @param $output
     * @return DoctrineQuery|QueryBuilder|Pagination|array|string|int|null
     */
    public function get($output = 'result')
    {
        switch($output){
            case 'qb':
                $this->qb->setParameters($this->parameters);
                return $this->qb;
                break;
            case 'query':
                return $this->get('qb')->getQuery();
                break;
            case 'result':
                return $this->get('query')->getResult();
                break;
            case 'sql':
                return $this->get('query')->getSQL();
                break;
            case 'pagination':
                $query = $this->get('query');
                return $this->provider->get('knp_paginator')->paginate($query, 1, 1);
                break;
            case 'count':
                return count($this->result());
                break;
        }

        throw new \InvalidArgumentException(sprintf('Invalid %s output option', $output));
    }

    /**
     * Initialize Query
     */
    private function initialize()
    {
        $this->qb = $this->provider->get('em')->createQueryBuilder();

        $this->qb->select('t')
            ->from(Transaction::class, 't')
            ->orderBy('t.createdAt', 'desc')
        ;
    }
}
