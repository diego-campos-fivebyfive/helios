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

        $config = [
            'manager' => $inverterManager,
            'maker' => $inverterMakerId
        ];

        $inverterLoader = InverterLoader::create($config);

        $inverters = $inverterLoader->filter($level);

        $stringBoxManager = $this->container->get('string_box_manager');

        $config = [
            'manager' => $stringBoxManager,
            'maker' => $stringBoxMakerId
        ];

        $stringBoxLoader = StringBoxLoader::create($config);

        $stringboxes = $stringBoxLoader->filter($level);

        $parameters['module'] = $project->getProjectModules()->first();
        $parameters['inverters'] = $inverters;
        $parameters['string_boxes'] = $stringboxes;
        $parameters['power'] = $power;
        $parameters['fdi_min'] = $fdiMin;
        $parameters['fdi_max'] = $fdiMax;
        $parameters['phase_voltage'] = $phaseVoltage;
        $parameters['phase_number'] = $phaseNumber;

        return $result = Core::process($parameters);
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