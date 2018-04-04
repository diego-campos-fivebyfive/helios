<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Manager\InverterManager;
use Doctrine\Common\Inflector\Inflector;

/**
 * Class InverterLoader
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class InverterLoader
{
    /**
     * @var array
     */
    private $config = [
        'manager' => null,
        'maker' => null
    ];

    /**
     * @var InverterManager
     */
    private $manager;

    /**
     * @var string
     */
    private $properties = 'i.id, i.generatorLevels levels, i.alternative, i.phases phaseNumber, i.phaseVoltage,
        i.compatibility, i.nominalPower, i.minPowerSelection, i.maxPowerSelection, i.mpptParallel,
        i.mpptNumber, i.mpptMin, i.inProtection, i.maxDcVoltage, i.mpptMaxDcCurrent';

    /**
     * InverterLoader constructor.
     * @param array $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
        $this->manager = $this->config['manager'];
    }

    /**
     * @param array $config
     * @return InverterLoader
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
        $qb = $this->manager->createQueryBuilder();

        $qb->select($this->properties)
            ->where('i.maker = :maker')
            ->orderBy('i.nominalPower', 'ASC')
            ->setParameter('maker', $this->config['maker']);

        return $this->formatKeys($qb->getQuery()->getResult());
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
        }, $qb2->select("DISTINCT(i.alternative)")
            ->where('i.alternative > 0')
            ->getQuery()->getResult()
        );

        if ($alternatives) {
            $results = $qb->select($this->properties)
                ->where(
                $qb->expr()->andX(
                    $qb->expr()->in(
                        'i.id',
                        $alternatives
                    ),
                    'i.maker != :maker'
                ))
                ->orderBy('i.nominalPower', 'ASC')
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
