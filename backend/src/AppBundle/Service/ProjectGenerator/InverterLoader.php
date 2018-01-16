<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Manager\InverterManager;
use Doctrine\ORM\QueryBuilder;

class InverterLoader
{
    /**
     * @var array
     */
    private $cache = [
        'enabled' => true,
        'sorted' => false,
        'queries' => 0,
        'time' => 0
    ];

    /**
     * @inheritDoc
     */
    public function __construct(InverterManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $defaults
     * @return array
     */
    public function load(array &$defaults)
    {
        $method = self::method($defaults);

        $phaseNumber = $defaults['phases'];
        $phaseVoltage = $defaults['voltage'];
        $power = (float) $defaults['power'];
        $maker = $defaults['inverter_maker'];

        $attempts = 1;
        $increments = 0;
        $start = microtime(true);

        do {

            $combine = false;
            $min = ($power * (float) $defaults['fdi_min']);
            $max = ($power * (float) $defaults['fdi_max']);
            $attemptCycle = 0;

            $collection = [];

            $config = [
                'min' => $min,
                'max' => $max,
                'phases' => $phaseNumber,
                'voltage' => $phaseVoltage,
                'maker' => $maker,
                'order' => 'asc',
                'cache' => $this->cache['enabled'],
                'power' => $power
            ];

            if(!$config['cache']) {

                $inverters = $this->$method($config);

            }else {

                $collection = $this->$method($config);

                $inverters = $this->filterInverters($collection, $config);
            }

            if (!count($inverters)) {
                for ($i = 300; $i >= 0; $i -= 15) {

                    $attempts++;
                    $attemptCycle++;

                    if(!$config['cache']) {

                        $inverters = $this->$method([
                            'min' => ($min * ($i / 1000)),
                            'max' => $max,
                            'phases' => $phaseNumber,
                            'voltage' => $phaseVoltage,
                            'maker' => $maker,
                            'order' => 'desc',
                            'cache' => $this->cache['enabled']
                        ]);

                    }else {

                        $inverters = $this->filterInverters($collection, [
                            'min' => ($min * ($i / 1000)),
                            'max' => $max,
                            'order' => 'desc'
                        ]);
                    }

                    if (count($inverters) >= 2) {
                        $combine = true;
                        break;
                    }
                }
            } else {
                array_splice($inverters, 1);
                $inverters[0]->quantity = 1;
            }

            $forceNext = (!count($inverters) || (count($inverters) <= 1 && $attemptCycle > 0));

            if ($forceNext) {
                $power += 0.2;
                $increments += 1;
            }

            if ($combine) {
                $inverters = array_values($inverters);
                if(!InverterCombiner::combine($inverters, $min)){
                    $defaults['errors'][] = 'exhausted_inverters';
                }
            }

        } while ($forceNext);

        $defaults['power'] = $power;
        $defaults['power_increments'] = $increments;

        $this->cache['time'] = microtime(true) - $start;

        $defaults['cache']['inverters'] = $this->cache;

        return $inverters;
    }

    /**
     * Configurations
     * 127/220 Monophasic
     *
     * @param array $config
     * @return array
     */
    private function find127220Monophasic(array $config)
    {
        return $this->findSharedPhasesAndVoltagesAware($config);
    }

    /**
     * Configurations
     * 127/220 Biphasic
     *
     * @param array $config
     * @return array
     */
    private function find127220Biphasic(array $config)
    {
        return $this->findSharedPhasesAndVoltagesAware($config);
    }

    /**
     * Configurations
     * 127/220 Triphasic
     *
     * @param array $config
     * @return array
     */
    private function find127220Triphasic(array $config)
    {
        $class = $this->manager->getClass();

        $qb = $this->createQueryBuilder();
        $qb2 = $this->createQueryBuilder();
        $qb3 = $this->createQueryBuilder();

        $qb->select('i')
            ->from($class, 'i')
            ->where(
                $qb->expr()->in('i.id',
                    $qb2
                        ->select('i2.id')
                        ->from($class, 'i2')
                        ->where(
                            $qb2->expr()->andX(

                                $qb2->expr()->orX(
                                    $qb2->expr()->andX(
                                        $qb2->expr()->eq('i2.phases', ':phases')
                                    ),
                                    $qb2->expr()->andX(
                                        $qb2->expr()->lt('i2.phases', ':phases')
                                    )
                                ),

                                $qb2->expr()->andX(
                                    $qb2->expr()->eq('i2.maker', ':maker')
                                )

                            )
                        )
                        ->getQuery()
                        ->getDQL()
                )
            )
            ->orWhere(
                $qb->expr()->notIn('i.compatibility',
                    $qb3
                        ->select('i3.compatibility')
                        ->from($class, 'i3')
                        ->where(
                            $qb3->expr()->andX(
                                $qb3->expr()->eq('i3.phases', ':phases'),
                                $qb3->expr()->eq('i3.phaseVoltage', ':phaseVoltage'),
                                $qb3->expr()->eq('i3.maker', ':maker')
                            )
                        )
                        ->getQuery()
                        ->getDQL()
                )
            )
            ->andWhere('i.maker = :maker')
            ->orderBy('i.nominalPower', $config['order']);

        $parameters = [
            'phases' => $config['phases'],
            'phaseVoltage' => $config['voltage'],
            'maker' => $config['maker']
        ];

        if(!$config['cache']){

            $qb->andWhere($qb->expr()->between('i.nominalPower', ':min', ':max'));

            $parameters['min'] = $config['min'];
            $parameters['max'] = $config['max'];
        }

        $this->finishCriteria($qb);

        $qb->setParameters($parameters);

        $this->cache['queries'] += 1;

        return $qb->getQuery()->getResult();
    }

    /**
     * Configurations
     * 220/380 Monophasic
     *
     * @param array $config
     * @return array
     */
    private function find220380Monophasic(array $config)
    {
        return $this->findSharedPhasesAndVoltagesAware($config);
    }

    /**
     * Configurations
     * 220/380 Biphasic
     *
     * @param array $config
     * @return array
     */
    private function find220380Biphasic(array $config)
    {
        return $this->findSharedPhasesAndVoltagesAware($config);
    }

    /**
     * Configurations
     * 220/380 Triphasic
     *
     * @param array $config
     * @return array
     */
    private function find220380Triphasic(array $config)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select('i')
            ->from($this->manager->getClass(), 'i')
            ->andWhere('i.maker = :maker')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('i.phases', ':phases'),
                        $qb->expr()->eq('i.phaseVoltage', ':phaseVoltage380')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->lt('i.phases', ':phases'),
                        $qb->expr()->eq('i.phaseVoltage', ':phaseVoltage220')
                    )
                )
            )
            ->orderBy('i.nominalPower', $config['order'])
        ;

        $parameters = [
            'phases' => $config['phases'],
            'phaseVoltage380' => 380,
            'phaseVoltage220' => 220,
            'maker' => $config['maker']
        ];

        if(!$config['cache']){

            $qb->andWhere($qb->expr()->between('i.nominalPower', ':min', ':max'));

            $parameters['min'] = $config['min'];
            $parameters['max'] = $config['max'];
        }

        $this->finishCriteria($qb);

        $qb->setParameters($parameters);

        $this->cache['queries'] += 1;

        return $qb->getQuery()->getResult();
    }

    /**
     * Configurations
     * 220/380 Monophasic
     * 220/380 Biphasic
     * 127/220 Biphasic
     *
     * @param array $config
     * @return array
     */
    private function findSharedPhasesAndVoltagesAware(array $config)
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select('i')
            ->from($this->manager->getClass(), 'i')
            ->andWhere('i.maker = :maker')
            ->andWhere('i.phases < :phaseNumber')
            ->andWhere('i.phaseVoltage = :phaseVoltage')
            ->orderBy('i.nominalPower', $config['order'])
        ;

        $parameters = [
            'maker' => $config['maker'],
            'phaseNumber' => 3,
            'phaseVoltage' => 220
        ];

        if(!$config['cache']){

            $qb->andWhere($qb->expr()->between('i.nominalPower', ':min', ':max'));

            $parameters['min'] = $config['min'];
            $parameters['max'] = $config['max'];
        }

        $this->finishCriteria($qb);

        $qb->setParameters($parameters);

        $this->cache['queries'] += 1;

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $inverters
     * @param array $config
     * @return array
     */
    private function filterInverters(array &$inverters, array $config)
    {
        if('desc' == $config['order'] && !$this->cache['sorted']){

            usort($inverters, function (Inverter $a, Inverter $b){
                return $b->getNominalPower() > $a->getNominalPower();
            });

            $this->cache['sorted'] = true;
        }

        $filtered = array_filter($inverters, function (Inverter $inverter) use($config){
            $minPowerSelection = $inverter->getMinPowerSelection();
            if (!is_null($minPowerSelection))
                if ($config['power'] < $minPowerSelection)
                    return false;

            $nominalPower = $inverter->getNominalPower();
            return $nominalPower >= $config['min'] && $nominalPower <= $config['max'];
        });

        return $filtered;
    }

    /**
     * @param array $defaults
     * @return string
     */
    private function method(array &$defaults)
    {
        $phaseOptions = [
            'Monophasic' => 1,
            'Biphasic' => 2,
            'Triphasic' => 3
        ];

        $first = str_replace('/', '', $defaults['grid_voltage']);
        $last = ucfirst(strtolower($defaults['grid_phase_number']));

        list($offset, $voltage) = explode('/', $defaults['grid_voltage']);

        $defaults['voltage'] = $voltage;
        $defaults['phases'] = $phaseOptions[$defaults['grid_phase_number']];

        return sprintf('find%s%s', $first, $last);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->manager->getEntityManager();
    }

    /**
     * @return QueryBuilder
     */
    private function createQueryBuilder()
    {
        return $this->getEntityManager()->createQueryBuilder();
    }

    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     */
    private function finishCriteria(QueryBuilder $qb, array &$criteria = [])
    {
        CriteriaAggregator::finish($criteria, $qb);
    }
}
