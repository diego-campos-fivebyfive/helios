<?php

namespace AppBundle\Service\ProjectGenerator;

use Doctrine\ORM\QueryBuilder;

class CriteriaAggregator
{
<<<<<<< HEAD
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
=======
    private static $arguments = [
        'promotional' => false,
        'status' => true,
        'available' => true
    ];

    /**
     * @param array $criteria
     * @param QueryBuilder|null $queryBuilder
     */
    public static function finish(array &$criteria = [], QueryBuilder $queryBuilder = null)
    {
        self::normalize();

        if($queryBuilder){

            $alias = self::alias($queryBuilder);

            foreach (self::$arguments as $argument => $value) {
                $queryBuilder->andWhere(sprintf('%s.%s = :%s', $alias, $argument, $argument));
            }
        }

        $criteria = array_merge($criteria, self::$arguments);
    }

    /**
     * @param bool $promotional
     */
    public static function promotional($promotional)
    {
        self::$arguments['promotional'] = (bool) $promotional;
    }

    /**
     * Normalize promotional argument for prevent inverted strict load
     */
    private static function normalize()
    {
        if(array_key_exists('promotional', self::$arguments) && !self::$arguments['promotional']){
            unset(self::$arguments['promotional']);
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return string
     */
    private static function alias(QueryBuilder $queryBuilder)
    {
        $aliases = $queryBuilder->getRootAliases();

        return $aliases[0];
>>>>>>> 18c5c8667d6ad16d87ddf9146b7ad4cc54b5a861
    }
}