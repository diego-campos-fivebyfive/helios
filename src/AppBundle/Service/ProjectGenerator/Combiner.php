<?php

namespace AppBundle\Service\ProjectGenerator;

class Combiner
{
    /**
     * @param array $inverters
     * @param Module $module
     */
    public static function combine(array &$inverters, Module $module)
    {
        $totalPower = 0;
        /** @var Inverter $inverter */
        foreach($inverters as $inverter){
            $totalPower += $inverter->nominalPower;
        }

        $tnoct  = 45;
        $tc_max = 70;

        $percentPower = [];
        foreach ($inverters as $inverter){
            $percentPower[] = $inverter->nominalPower / $totalPower;
        }
        $vmax_mod = $module->openCircuitVoltage;
        $vmin_mod = $module->voltageMaxPower * (1 + (($tc_max - $tnoct) * ($module->tempCoefficientVoc / 100)));
        foreach ($inverters as $key => $inverter){
            $qte_max_mod_ser = floor($inverter->maxDcVoltage / $vmax_mod);
            $qte_min_mod_ser = ceil($inverter->mpptMin / $vmin_mod);
            $qte_max_mod_par = floor(($inverter->mpptMaxCcCurrent * $inverter->mpptNumber) / ($module->shortCircuitCurrent));

            for ($p = 1; $p <= $qte_max_mod_par; $p++) {
                for ($s = $qte_min_mod_ser; $s <= $qte_max_mod_ser; $s++) {
                    $pot = ($p * $s) * ($module->maxPower / 1000);
                    $n_mod = $p * $s;
                    if ($pot >= ($module->power * $percentPower[$key])) {
                        $inverters[$key]->serial = (int) $s;
                        $inverters[$key]->parallel = (int) $p; // NRO DE STRINGS
                        break 2;
                    }
                }
            }
        }

        /** @var Inverter $inverter */
        foreach ($inverters as $inverter){
            $module->quantity += $inverter->count();
        }
    }
}