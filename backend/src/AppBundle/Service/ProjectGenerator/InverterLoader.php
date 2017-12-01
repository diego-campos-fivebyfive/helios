<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Manager\InverterManager;
use Doctrine\ORM\QueryBuilder;

class InverterLoader
{

    /**
     * @var bool
     */
    private $promotional = false;

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

        $this->promotional = $defaults['is_promotional'];

        $phaseNumber = $defaults['phases'];
        $phaseVoltage = $defaults['voltage'];

        $power = (float) $defaults['power'];
        $maker = $defaults['inverter_maker'];

        $attempts = 1;
        $increments = 0;

        do {

            $combine = false;
            $min = ($power * (float) $defaults['fdi_min']);
            $max = ($power * (float) $defaults['fdi_max']);
            $attemptCycle = 0;

            $inverters = $this->$method($min, $max, $phaseNumber, $phaseVoltage, $maker, 'asc');

            if (!count($inverters)) {
                for ($i = 300; $i >= 0; $i -= 15) {

                    $attempts++;
                    $attemptCycle++;

                    $inverters = $this->$method(($min * ($i / 1000)), $max, $phaseNumber, $phaseVoltage, $maker, 'desc');

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
                if(!InverterCombiner::combine($inverters, $min)){
                    $defaults['errors'][] = 'exhausted_inverters';
                }
            }

        } while ($forceNext);

        $defaults['power'] = $power;
        $defaults['power_increments'] = $increments;

        return $inverters;
    }

    /**
     * Configurations
     * 127/220 Monophasic
     *
     * @param $min
     * @param $max
     * @param $phaseNumber
     * @param $phaseVoltage
     * @param $maker
     * @param string $order
     * @return array
     */
    private function find127220Monophasic($min, $max, $phaseNumber, $phaseVoltage, $maker, $order = 'asc')
    {
        return $this->findSharedPhasesAndVoltagesAware($min, $max, $phaseNumber, $phaseVoltage, $maker, $order);
    }

    /**
     * Configurations
     * 127/220 Biphasic
     *
     * @param $min
     * @param $max
     * @param $phaseNumber
     * @param $phaseVoltage
     * @param $maker
     * @param string $order
     * @return array
     */
    private function find127220Biphasic($min, $max, $phaseNumber, $phaseVoltage, $maker, $order = 'asc')
    {
        return $this->findSharedPhasesAndVoltagesAware($min, $max, $phaseNumber, $phaseVoltage, $maker, $order);
    }

    /**
     * Configurations
     * 127/220 Triphasic
     *
     * @param $min
     * @param $max
     * @param $phaseNumber
     * @param $phaseVoltage
     * @param $maker
     * @param string $order
     * @return array
     */
    private function find127220Triphasic($min, $max, $phaseNumber, $phaseVoltage, $maker, $order = 'asc')
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
                                        //$qb2->expr()->eq('i2.phaseVoltage', ':phaseVoltage')
                                    ),
                                    $qb2->expr()->andX(
                                        $qb2->expr()->lt('i2.phases', ':phases')
                                        //$qb2->expr()->eq('i2.phaseVoltage', ':phaseVoltage')
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
            ->andWhere($qb->expr()->between('i.nominalPower', ':min', ':max'))
            ->orderBy('i.nominalPower', $order)
        ;

        $parameters = [
            'min' => $min,
            'max' => $max,
            'phases' => $phaseNumber,
            'phaseVoltage' => $phaseVoltage,
            'maker' => $maker
        ];

        $this->finishCriteria($qb, $parameters);

        $qb->setParameters($parameters);

        return $qb->getQuery()->getResult();
    }

    /**
     * Configurations
     * 220/380 Monophasic
     *
     * @param $min
     * @param $max
     * @param $phaseNumber
     * @param $phaseVoltage
     * @param $maker
     * @param string $order
     * @return array
     */
    private function find220380Monophasic($min, $max, $phaseNumber, $phaseVoltage, $maker, $order = 'asc')
    {
        return $this->findSharedPhasesAndVoltagesAware($min, $max, $phaseNumber, $phaseVoltage, $maker, $order);
    }

    /**
     * Configurations
     * 220/380 Biphasic
     *
     * @param $min
     * @param $max
     * @param $phaseNumber
     * @param $phaseVoltage
     * @param $maker
     * @param string $order
     * @return array
     */
    private function find220380Biphasic($min, $max, $phaseNumber, $phaseVoltage, $maker, $order = 'asc')
    {
        return $this->findSharedPhasesAndVoltagesAware($min, $max, $phaseNumber, $phaseVoltage, $maker, $order);
    }

    /**
     * Configurations
     * 220/380 Triphasic
     *
     * @param $min
     * @param $max
     * @param $phaseNumber
     * @param $phaseVoltage
     * @param $maker
     * @param string $order
     * @return array
     */
    private function find220380Triphasic($min, $max, $phaseNumber, $phaseVoltage, $maker, $order = 'asc')
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select('i')
            ->from($this->manager->getClass(), 'i')
            ->where($qb->expr()->between('i.nominalPower', ':min', ':max'))
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
            ->orderBy('i.nominalPower', $order)
        ;

        $parameters = [
            'min' => $min,
            'max' => $max,
            'phases' => $phaseNumber,
            'phaseVoltage380' => 380,
            'phaseVoltage220' => 220,
            'maker' => $maker
        ];

        $this->finishCriteria($qb);

        $qb->setParameters($parameters);

        return $qb->getQuery()->getResult();
    }

    /**
     * Configurations
     * 220/380 Monophasic
     * 220/380 Biphasic
     * 127/220 Biphasic
     *
     * @param $min
     * @param $max
     * @param $phaseNumber
     * @param $phaseVoltage
     * @param $maker
     * @param string $order
     * @return array
     */
    private function findSharedPhasesAndVoltagesAware($min, $max, $phaseNumber, $phaseVoltage, $maker, $order = 'asc')
    {
        $qb = $this->createQueryBuilder();

        $qb
            ->select('i')
            ->from($this->manager->getClass(), 'i')
            ->where($qb->expr()->between('i.nominalPower', ':min', ':max'))
            ->andWhere('i.maker = :maker')
            ->andWhere('i.phases < :phaseNumber')
            ->andWhere('i.phaseVoltage = :phaseVoltage')
            ->orderBy('i.nominalPower', $order)
        ;

        $parameters = [
            'min' => $min,
            'max' => $max,
            'maker' => $maker,
            'phaseNumber' => 3,
            'phaseVoltage' => 220
        ];

        $this->finishCriteria($qb);

        $qb->setParameters($parameters);

        return $qb->getQuery()->getResult();
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
