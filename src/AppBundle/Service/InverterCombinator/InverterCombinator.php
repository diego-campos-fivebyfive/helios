<?php

namespace AppBundle\Service\InverterCombinator;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Manager\InverterManager;

class InverterCombinator
{
    /**
     * @var InverterLoader
     */
    private $loader;

    /**
     * InverterCombinator constructor.
     * @param InverterManager $manager
     */
    function __construct(InverterManager $manager)
    {
        $this->loader = new InverterLoader($manager);
    }

    public function combine(ModuleInterface $module, $power, $maker)
    {
        $this->loader->power($power);
        $this->loader->maker($maker);

        $inverters = $this->loader->get();

        $this->distribute($inverters, [
            'power' => $power,
            'open_circuit_voltage' => $module->getOpenCircuitVoltage(),
            'voltage_max_power' => $module->getVoltageMaxPower(),
            'temp_coefficient_voc' => $module->getTempCoefficientVoc(),
            'short_circuit_current' => $module->getShortCircuitCurrent(),
            'max_power' => $module->getMaxPower()
        ]);

        return $inverters;
    }

    /**
     * @param array $inverters
     * @param array $module
     */
    public function distribute(array &$inverters, array $module)
    {
        $totalPower = 0;
        foreach($inverters as $inverter){
            $totalPower += $inverter['nominal_power'];
        }

        $percentPower = [];
        foreach ($inverters as $inverter){
            $percentPower[] = $inverter['nominal_power'] / $totalPower;
        }

        $tnoct  = 45;
        //$tc_min = 10;
        $tc_max = 70;
        $vmax_mod = $module['open_circuit_voltage'];
        $vmin_mod = $module['voltage_max_power'] * (1 + (($tc_max - $tnoct) * ($module['temp_coefficient_voc'] / 100)));

        foreach ($inverters as $key => $inverter){
            $qte_max_mod_ser = floor($inverter["max_dc_voltage"] / $vmax_mod);
            $qte_min_mod_ser = ceil($inverter["mppt_min"] / $vmin_mod);
            $qte_max_mod_par = floor(($inverter["mppt_max_dc_current"] * $inverter["mppt_number"]) / ($module['short_circuit_current']));

            for ($p = 1; $p <= $qte_max_mod_par; $p++) {
                for ($s = $qte_min_mod_ser; $s <= $qte_max_mod_ser; $s++) {
                    $pot = ($p * $s) * ($module['max_power'] / 1000);
                    $n_mod = $p * $s;
                    if ($pot >= ($module['power'] * $percentPower[$key])) {
                        $inverters[$key]["serial"] = (int) $s;
                        $inverters[$key]["parallel"] = (int) $p; // NRO DE STRINGS
                        break 2;
                    }
                }
            }
        }
    }
}