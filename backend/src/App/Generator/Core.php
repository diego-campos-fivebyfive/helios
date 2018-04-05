<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Generator;

use App\Generator\Inverter\Helper as InverterHelper;
use App\Generator\StringBox\Helper as StringBoxHelper;

/**
 * Class Core
 */
class Core
{
    /**
     * @param array $parameters
     * @return array
     */
    public function process(array $parameters)
    {
        $this->resolveDefaults($parameters);

        $module = $parameters['module'];
        $inverters = $parameters['inverters'];
        $stringBoxes = $parameters['string_boxes'];
        $power = $parameters['power'];
        $fdiMin = $parameters['fdi_min'];
        $fdiMax = $parameters['fdi_max'];
        $phaseVoltage = $parameters['phase_voltage'];
        $phaseNumber = $parameters['phase_number'];

        $inverters = InverterHelper::filterPhases($inverters, $phaseVoltage, $phaseNumber);
        $power = InverterHelper::adjustPower($inverters, $power, $fdiMax);
        $inverters = InverterHelper::filterPower($inverters, $power);
        $inverters = InverterHelper::inverterChoices($inverters, $power, $fdiMin, $fdiMax);
        $power = InverterHelper::powerBalance($inverters, $power);
        $mpptOperations = InverterHelper::mpptOperations($inverters);

        return [
            'module' => $module,
            'inverters' => $inverters,
            'arragements' => [],
            'string_boxes' => []
        ];
    }

    /**
     * @param $parameters
     */
    private function resolveDefaults(&$parameters)
    {
        $parameters['string_boxes'] = $parameters['string_boxes'] ?? [];
        $parameters['power'] = $parameters['power'] ?? 0;
        $parameters['fdi_min'] = $parameters['fdi_min'] ?? 0.75;
        $parameters['fdi_max'] = $parameters['fdi_max'] ?? 1.3;
        $parameters['phase_voltage'] = $parameters['phase_voltage'] ?? 220;
        $parameters['phase_number'] = $parameters['phase_number'] ?? 1;
    }
}