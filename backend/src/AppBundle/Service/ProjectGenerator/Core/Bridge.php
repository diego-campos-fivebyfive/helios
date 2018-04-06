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
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectStringBox;
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

        $this->stringBoxResolution($result, $stringBoxLoader, $project);
    }

    /**
     * @param $data
     * @param StringBoxLoader $stringBoxLoader
     * @param ProjectInterface $project
     */
    private function stringBoxResolution($data, StringBoxLoader $stringBoxLoader, ProjectInterface $project)
    {
        $stringBoxesIds = array_column($data['string_boxes'], 'id');

        $stringBoxesQuantities = array_count_values($stringBoxesIds);

        $stringBoxesId = array_unique($stringBoxesIds);

        $stringBoxes = $stringBoxLoader->findByIds($stringBoxesId);

        foreach ($stringBoxes as $stringBox) {
            $projectStringBox = new ProjectStringBox();

            $projectStringBox->setStringBox($stringBox);
            $projectStringBox->setProject($project);
            $projectStringBox->setQuantity($stringBoxesQuantities[$stringBox->getId()]);
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