<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use Doctrine\Common\Inflector\Inflector;

abstract class AbstractLoader {

    /** @var array  */
    protected $config = [
        'manager' => null,
        'maker' => null
    ];

    /**
     * @var \AppBundle\Manager\AbstractManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $properties = "";

    /**
     * AbstractLoader constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->manager = $config['manager'];
    }

    /**
     * @return array
     */
    public abstract function all();

    /**
     * @return array
     */
    public abstract function alternatives();

    /**
     * @param array $ids
     * @return array
     */
    public function findByIds(array $ids)
    {
        $qb = $this->manager->createQueryBuilder();

        $alias = $qb->getRootAlias();

        $qb->select()
            ->where(
                $qb->expr()->in("{$alias}.id", $ids)
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $level
     * @return array
     */
    protected function filter($level)
    {
        return FilterLevelTrait::filterActives($level, $this->all(), $this->alternatives());
    }

    /**
     * @param $data
     * @return array
     */
    protected function formatKeys($data)
    {
        return array_map(function ($arrayInverter) {
            $keys = array_map(function ($key) {
                return Inflector::tableize($key);
            }, array_keys($arrayInverter));

            return array_combine($keys, $arrayInverter);
        }, $data);
    }

}
