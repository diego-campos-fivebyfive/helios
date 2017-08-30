<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator\Checker;

use AppBundle\Entity\Component\ComponentTrait;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Entity\Component\Structure;
use AppBundle\Service\ProjectGenerator\AbstractConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class provides the functionality of parameter checking for project generation
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Checker
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $result = [
        'modules' => [],
        'inverter_makers' => []
    ];

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var bool
     */
    private $promotional = false;

    /**
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param array $defaults
     * @return array
     */
    public function checkDefaults(array $defaults)
    {
        $this->promotional = $defaults['is_promotional'];

        $this->checkModules();
        $this->checkInverterMakers();
        $this->checkStringBoxMakers();
        $this->checkStructureMakers();
        $this->checkGridVoltages();
        $this->checkGridPhaseNumbers();

        return $this->result;
    }

    /**
     * @return array
     */
    public function checkModules()
    {
        $filter = $this->createFilter(Module::class);

        if($this->promotional){
            $filter->equals('promotional', true);
        }

        $modules = $filter->get();

        $this->result['modules'] = $modules;

        return $modules;
    }

    /**
     * @return array
     */
    public function checkInverterMakers()
    {
        $filter = $this->createFilter(Inverter::class);

        if($this->promotional){
            $filter->equals('promotional', true);
        }

        $filter->group('maker');

        $makers = $this->filterMakers($filter->get());

        $this->result['inverter_makers'] = $makers;

        return $makers;
    }

    /**
     * @return array
     */
    public function checkStringBoxMakers()
    {
        $filter = $this->createFilter(StringBox::class);

        if($this->promotional){
            $filter->equals('promotional', true);
        }

        $filter->group('maker');

        $makers = $this->filterMakers($filter->get());

        $this->result['string_box_makers'] = $makers;

        return $makers;
    }

    /**
     * @return array
     */
    public function checkStructureMakers()
    {
        $filter = $this->createFilter(Structure::class);

        if($this->promotional){
            $filter->equals('promotional', true);
        }

        $filter->group('maker');

        $makers = $this->filterMakers($filter->get());

        $this->result['structure_makers'] = $makers;

        return $makers;
    }

    /**
     * @return array
     */
    public function checkGridVoltages()
    {
        $voltages = array_combine(AbstractConfig::getVoltages(), AbstractConfig::getVoltages());

        $this->result['grid_voltages'] = $voltages;

        return $voltages;
    }

    /**
     * @return array
     */
    public function checkGridPhaseNumbers()
    {
        $phaseNumbers = array_combine(AbstractConfig::getPhaseNumbers(), AbstractConfig::getPhaseNumbers());

        $this->result['grid_phase_numbers'] = $phaseNumbers;

        return $phaseNumbers;
    }

    /**
     * @param array $data
     * @return array
     */
    private function filterMakers(array $data)
    {
        return array_map(function($component){
            return $component->getMaker();
        }, $data);
    }

    /**
     * @return Filter
     */
    private function createFilter($class)
    {
        $filter = new Filter($this->em);
        $filter->fromClass($class);

        return $filter;
    }
}