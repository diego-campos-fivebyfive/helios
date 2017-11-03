<?php

namespace AppBundle\Service\Stock;

use AppBundle\Entity\Stock\Product;
use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Entity\Stock\Transaction;
use AppBundle\Manager\Stock\ProductManager;
use AppBundle\Service\Filter\AbstractFilter;
use AppBundle\Service\Stock\Provider;
use Doctrine\ORM\QueryBuilder;

class QueryTransaction
{
    /**
     * @var
     */
    private $criteria = [];

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

        $this->qb = $provider->get('em')->createQueryBuilder();

        $this->qb->select('t')
            ->from(Transaction::class, 't')
            ->join('t.product', 'p')
        ;
    }

    /**
     * @param ProductInterface $product
     */
    public function product(ProductInterface $product)
    {
        $this->qb->andWhere('p.id = :product');
        $this->parameters['product'] = $product;
    }

    /**
     * @param \DateTime $startAt
     * @param \DateTime $endAt
     */
    public function between(\DateTime $startAt, \DateTime $endAt)
    {
        $this->qb->andWhere('t.createdAt >= :startAt');
        $this->qb->andWhere('t.createdAt <= :endAt');

        $this->parameters['startAt'] = $startAt->format('Y-m-d 00:00:00');
        $this->parameters['endAt'] = $endAt->format('Y-m-d 23:59:59');
    }

    public function criteria(array $criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @param $output
     * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder|array|null
     */
    public function get($output)
    {
        switch($output){
            case 'qb':
                $this->qb->setParameters($this->parameters);
                return $this->qb;
                break;
            case 'query':
                return $this->qb->getQuery();
                break;
            case 'sql':
                return $this->get('query')->getSQL();
                break;
            case 'result':
                return $this->get('query')->getResult();
                break;
        }

        return null;
    }
}
