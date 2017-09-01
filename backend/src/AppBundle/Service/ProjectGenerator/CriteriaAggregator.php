<?php

namespace AppBundle\Service\ProjectGenerator;

use Doctrine\ORM\QueryBuilder;

class CriteriaAggregator
{
    public static $promotional = false;

    /**
     * @param array $criteria
     * @param QueryBuilder|null $qb
     */
    public static function promotional(array &$criteria = [], QueryBuilder $qb = null)
    {
        if(self::$promotional){

            if($qb instanceof QueryBuilder){
                self::queryBuilder($qb);
            }

            self::criteria($criteria);
        }
    }

    /**
     * @param QueryBuilder $qb
     */
    private static function queryBuilder(QueryBuilder $qb)
    {
        $aliases = $qb->getRootAliases();
        $qb->andWhere($aliases[0] . '.promotional = :promotional');
    }

    /**
     * @param array $criteria
     */
    private static function criteria(array &$criteria = [])
    {
        $criteria['promotional'] = self::$promotional;
    }
}