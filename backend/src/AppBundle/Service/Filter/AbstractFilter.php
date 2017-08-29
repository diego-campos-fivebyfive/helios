<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Filter;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

/**
 * This class provides the functionality required for fast filters on entities
 * This class will be signed as abstract soon.
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
/*abstract*/ class AbstractFilter
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $alias;

    /**
     * AbstractFilter constructor.
     * @param EntityManagerInterface $manager
     */
    function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $object
     * @return $this
     */
    public function fromObject($object)
    {
        return $this->fromClass(get_class($object));
    }

    /**
     * @param $class
     * @return $this
     */
    public function fromClass($class)
    {
        $this->class = $class;
        $this->alias = $this->buildAlias($class);

        $this->initialize();

        return $this;
    }

    /**
     * @param $target
     * @return AbstractFilter
     */
    public function from($target)
    {
        return is_object($target) ? $this->fromObject($target) : $this->fromClass($target);
    }

    /**
     * @return string
     */
    public function alias()
    {
        return $this->alias;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function query()
    {
        return $this->qb->getQuery();
    }

    /**
     * @return string
     */
    public function sql()
    {
        return $this->query()->getSQL();
    }

    /**
     * @return string
     */
    public function dql()
    {
        return $this->query()->getDQL();
    }

    /**
     * @return QueryBuilder
     */
    public function qb()
    {
        return $this->qb;
    }

    /**
     * @return array
     */
    public function get()
    {
        return $this->qb->getQuery()->getResult();
    }

    /**
     * @param $field
     * @param $value
     * @return AbstractFilter
     */
    public function equals($field, $value)
    {
        if(is_null($value)){
            return $this->isNull($field);
        }

        $this->qb
            ->andWhere($this->comparison($field, '='))
            ->setParameter(':' . $field, $value)
        ;

        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function notEquals($field, $value)
    {
        $this->qb
            ->andWhere($this->comparison($field, '<>'))
            ->setParameter($field, $value)
        ;

        return $this;
    }

    /**
     * @param $field
     * @param $start
     * @param $end
     * @return $this
     */
    public function between($field, $start, $end)
    {
        $this->qb->andWhere(
            $this->qb->expr()->between(self::field($field), $start, $end)
        );

        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function isNull($field)
    {
        $this->qb->andWhere(
            $this->qb->expr()->isNull(self::field($field))
        );

        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function notNull($field)
    {
        $this->qb->andWhere(
            $this->qb->expr()->isNotNull(self::field($field))
        );

        return $this;
    }

    /**
     * Initialize Query Builder
     */
    private function initialize()
    {
        $this->qb = $this
            ->manager
            ->createQueryBuilder()
            ->select($this->alias)
            ->from($this->class, $this->alias)
        ;
    }

    /**
     * @param $class
     * @return string
     */
    public function buildAlias($class)
    {
        $tokens = array_reverse(explode('\\', $class));

        $alias = strtolower(substr($tokens[0], 0, 1));

        return $alias;
    }

    /**
     * @param $field
     * @return string
     */
    private function field($field)
    {
        return sprintf('%s.%s', $this->alias, $field);
    }

    /**
     * @param $field
     * @param string $comparision
     * @return string
     */
    private function comparison($field, $comparision = '=')
    {
        return sprintf('%s.%s %s :%s', $this->alias, $field, $comparision, $field);
    }
}