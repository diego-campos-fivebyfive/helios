<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator\Core;

use App\Generator\Core;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Bridge
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Bridge constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ProjectInterface $project
     * @return array
     */
    public function resolve(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();

        $level = $this->getLevel($defaults);
        $fdiMin = $defaults['fdi_min'];
        $fdiMax = $defaults['fdi_max'];
        $power = $defaults['power'];
        $phaseVoltage = $defaults['voltage'];
        $phaseNumber = $defaults['phases'];
        $inverterMakerId = $defaults['inverter_maker'];
        $stringBoxMakerId = $defaults['string_box_maker'];

        $inverterManager = $this->container->get('inverter_manager');

        $inverterLoader = new InverterLoader([
            'manager' => $inverterManager,
            'maker' => $inverterMakerId
        ]);

        $invertersArray = $inverterLoader->filter($level);

        $stringBoxManager = $this->container->get('string_box_manager');

        $stringBoxLoader = new StringBoxLoader([
            'manager' => $stringBoxManager,
            'maker' => $stringBoxMakerId
        ]);

        $stringBoxesArray = $stringBoxLoader->filter($level);

        /** @var Module $module */
        $module = $project->getProjectModules()->first()->getModule();

        $module = [
            'id' => $module->getId(),
            'max_power' => $module->getMaxPower(),
            'voltage_max_power' => $module->getVoltageMaxPower(),
            'open_circuit_voltage' => $module->getOpenCircuitVoltage(),
            'short_circuit_current' => $module->getShortCircuitCurrent(),
            'temp_coefficient_voc' => $module->getTempCoefficientVoc()
        ];

        $parameters['module'] = $module;
        $parameters['inverters'] = $invertersArray;
        $parameters['string_boxes'] = $stringBoxesArray;
        $parameters['power'] = $power;
        $parameters['fdi_min'] = $fdiMin;
        $parameters['fdi_max'] = $fdiMax;
        $parameters['phase_voltage'] = $phaseVoltage;
        $parameters['phase_number'] = $phaseNumber;

        $result = Core::process($parameters);

        $this->inverterResolution($result, $inverterLoader, $project);

        $projectManager = $this->container->get('project_manager');

        $projectManager->save($project);
    }

    /**
     * @param $data
     * @param InverterLoader $inverterLoader
     * @param ProjectInterface $project
     */
    private function inverterResolution($data, InverterLoader $inverterLoader, ProjectInterface $project)
    {
        $invertersIds = array_column($data['inverters'], 'id');

        $invertersQuantities = array_count_values($invertersIds);

        $invertersId = array_unique($invertersIds);

        $inverters = $inverterLoader->findByIds($invertersId);

        foreach ($inverters as $inverter) {
            $projectInverter = new ProjectInverter();

            $projectInverter->setInverter($inverter);
            $projectInverter->setProject($project);
            $projectInverter->setQuantity($invertersQuantities[$inverter->getId()]);
        }
    }

    /**
     * @param $defaults
     * @return string
     */
    private function getLevel($defaults)
    {
        if (isset($defaults['finame']) || isset($defaults['is_promotional'])) {
            return isset($defaults['finame']) ? 'finame' : 'promotional';
        }

        return $defaults['level'];
    }
}