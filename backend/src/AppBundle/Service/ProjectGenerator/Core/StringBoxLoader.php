<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\StringBox;
use AppBundle\Manager\StringBoxManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Inflector\Inflector;

/**
 * Class StringBoxLoader
*  @author Gianluca Bine <gian_bine@hotmail.com>
*/
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
     * @var StringBoxManager
     */
    private $manager;

    /**
     * @var string
     */
    private $properties = 's.id, s.inputs, s.outputs, s.generatorLevels levels';

    /**
     * StringBoxLoader constructor.
     * @param array $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
        $this->manager = $this->config['manager'];
    }

    /**
     * @param array $config
     * @return StringBoxLoader
     */
    public static function create(array $config)
    {
        return new self($config);
    }

    /**
     * @return array
     */
    public function all()
    {
        /** @var QueryBuilder $qb */
        $qb = $this->config['manager']->createQueryBuilder();

        $qb->select($this->properties)
            ->addOrderBy('s.inputs', 'ASC')
            ->addOrderBy('s.outputs', 'ASC');

        if (!is_null($this->config['maker'])) {
            $qb->andWhere($qb->expr()->eq('s.maker', $this->config['maker']));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function alternatives()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb2 = $this->manager->createQueryBuilder();

        $alternatives = array_map(function ($alt) {
            return current($alt);
        }, $qb2->select("DISTINCT(s.alternative)")
            ->where('s.alternative > 0')
            ->getQuery()->getResult()
        );

        if ($alternatives) {
            $results = $qb->select($this->properties)
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->in(
                            's.id',
                            $alternatives
                        ),
                        's.maker != :maker'
                    ))
                ->addOrderBy('s.inputs', 'ASC')
                ->addOrderBy('s.outputs', 'ASC')
                ->setParameter('maker', $this->config['maker'])
                ->getQuery()->getResult();

            return $this->formatKeys($results);
        }

        return [];
    }

    /**
     * @param $level
     * @return array
     */
    public function filter($level)
    {
        return FilterLevelTrait::filterActives($level, $this->all(), $this->alternatives());
    }

    /**
     * @param $data
     * @return array
     */
    private function formatKeys($data)
    {
        return array_map(function ($arrayInverter) {
            $keys = array_map(function ($key) {
                return Inflector::tableize($key);
            }, array_keys($arrayInverter));

            return array_combine($keys, $arrayInverter);
        }, $data);
    }

}
