<?php

namespace AppBundle\Service\ProjectGenerator;

use Doctrine\ORM\QueryBuilder;

class CriteriaAggregator
{
    private static $level = null;

    /**
     * @param array $criteria
     * @param QueryBuilder|null $queryBuilder
     */
    public static function finish(array &$criteria = [], QueryBuilder $queryBuilder = null)
    {
        if($queryBuilder){

            $alias = self::alias($queryBuilder);

            foreach ($criteria as $argument => $value) {
                $queryBuilder->andWhere(sprintf('%s.%s = :%s', $alias, $argument, $argument));
                $queryBuilder->setParameter($argument, $value);
                unset($criteria[$argument]);
            }

            $queryBuilder->andWhere(
                $queryBuilder->expr()->like(sprintf('%s.generatorLevels', $alias),
                    $queryBuilder->expr()->literal('%"'.self::$level.'"%')
                )
            );

            $queryBuilder->addOrderBy(sprintf('%s.position', $alias), 'asc');
        }
    }

    /**
     * @param $level
     */
    public static function level($level)
    {
        self::$level = (string) $level;
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
