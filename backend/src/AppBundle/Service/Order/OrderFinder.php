<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Order;

use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Manager\OrderManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class OrderFinder
 * This class filters orders according to pre configured dynamic arguments
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderFinder
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * OrderFinder constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $parameter
     * @param $value
     * @return $this
     */
    public function set($parameter, $value)
    {
        $this->parameters[$parameter] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->query()->getResult();
    }

    /**
     * @return Order|null
     */
    public function first()
    {
        $orders = $this->all();

        return !empty($orders) ? $orders[0] : null ;
    }

    /**
     * @return Order|null
     */
    public function last()
    {
        $orders = $this->all();

        return !empty($orders) ? $orders[count($orders)-1] : null ;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function query()
    {
        return $this->queryBuilder()->getQuery();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryBuilder()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb
            ->leftJoin('o.childrens', 'c')
            ->leftJoin('c.elements', 'e')
        ;

        foreach ($this->parameters as $property => $value){
            $this->handleParameter($qb, $property);
        }

        $qb->setParameters($this->parameters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param $property
     * @return void
     */
    private function handleParameter(QueryBuilder $qb, $property)
    {
        $alias = $this->manager->alias();
        $check = sprintf(' = :%s', $property);

        switch ($property){

            case 'agent':
                $this->addAgentParameter($qb);
                return;
                break;

        }

        if(is_null($this->parameters[$property])){
            $check = 'is null';
            unset($this->parameters[$property]);
        }

        $qb->andWhere(sprintf('%s.%s %s', $alias, $property, $check));
    }

    /**
     * @param QueryBuilder $qb
     */
    private function addAgentParameter(QueryBuilder $qb)
    {
        /** @var MemberInterface $agent */
        $agent = $this->parameters['agent'];

        $includeStatus = [];
        if($agent->isPlatformAfterSales()){
            $includeStatus = [Order::STATUS_DONE];
        }

        if($agent->isPlatformFinancial()){
            $includeStatus = [Order::STATUS_APPROVED, Order::STATUS_REJECTED, Order::STATUS_DONE];
        }

        if(!empty($includeStatus)){
            $qb->andWhere($qb->expr()->in('o.status', $includeStatus));
            unset($this->parameters['agent']);
            return;
        }

        $exprAgent = null;
        if($agent->isPlatformCommercial()){
            $exprAgent = $qb->expr()->eq('o.agent', ':agent');
        }else{
            unset($this->parameters['agent']);
        }

        $qb
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('o.source', Order::SOURCE_ACCOUNT),
                        $qb->expr()->notIn('o.status', [Order::STATUS_BUILDING]),
                        $exprAgent
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('o.source', Order::SOURCE_PLATFORM),
                        $exprAgent
                    )
                )
            )
        ;
    }
}
