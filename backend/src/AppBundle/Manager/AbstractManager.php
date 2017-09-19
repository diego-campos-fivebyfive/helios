<?php

namespace AppBundle\Manager;

use Sonata\CoreBundle\Model\BaseEntityManager;

abstract class AbstractManager extends BaseEntityManager
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $alias = $this->alias();

        $qb->select($alias)->from($this->class, $alias);

        return $qb;
    }

    /**
     * @param $field
     * @param array $criteria
     * @return array
     */
    public function distinct($field, array $criteria = [])
    {
        $qb = $this->createQueryBuilder();

        $fieldAlias = sprintf('%s.%s', $this->alias(), $field);

        $qb->select(sprintf('distinct(%s)', $fieldAlias));

        foreach ($criteria as $property => $value){
            $qb->andWhere(sprintf('%s.%s = :%s', $this->alias(), $property, $property));
            $qb->setParameter($property, $value);
        }

        $result = $qb->getQuery()->getResult();

        return array_filter(array_map('current', $result));
    }

    public function alias()
    {
        return strtolower(substr(array_reverse(explode('\\', $this->class))[0], 0, 1));
    }
}
