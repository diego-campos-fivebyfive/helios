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

        $alias = strtolower(substr(array_reverse(explode('\\', $this->class))[0], 0, 1));

        $qb->select($alias)->from($this->class, $alias);

        return $qb;
    }
}