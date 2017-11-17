<?php

namespace AppBundle\Service\ProjectGenerator;

use Doctrine\ORM\QueryBuilder;

class CriteriaAggregator
{
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

            $queryBuilder->addOrderBy(sprintf('%s.position', $alias), 'asc');
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
    }
}
