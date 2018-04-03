<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Manager\StringBoxManager;
use Doctrine\ORM\QueryBuilder;

class StringBoxLoader
{

    /**
     * @var array
     */
    private $config = [
        'manager' => null,
        'maker' => null
    ];

    /**
     * StringBoxLoader constructor.
     * @param array $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public function all()
    {
        /** @var QueryBuilder $qb */
        $qb = $this->config['manager']->createQueryBuilder();

        $qb->select()
            ->addOrderBy('s.inputs', 'ASC')
            ->addOrderBy('s.outputs', 'ASC');

        if (!is_null($this->config['maker'])) {
            $qb->andWhere($qb->expr()->eq('s.maker', $this->config['maker']));
        }

        return $qb->getQuery()->getResult();
    }

    public function alternatives()
    {

    }

    /**
     * @param array $config
     * @return StringBoxLoader
     */
    public static function create(array $config)
    {
        return new self($config);
    }

}