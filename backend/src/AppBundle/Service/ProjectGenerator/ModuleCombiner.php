<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;

/**
 * Class ModuleCombiner
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class ModuleCombiner
{
    /**
     * @param ProjectInterface $project
     */
    public static function combine(ProjectInterface $project)
    {
        $defaults = $project->getDefaults();
        $power = $defaults['power'];

        /** @var \AppBundle\Entity\Component\ProjectModuleInterface $projectModule */
        $projectModule = $project->getProjectModules()->first();
        /** @var \AppBundle\Entity\Component\ModuleInterface $module */
        $module = $projectModule->getModule();
        $totalPower = 0;
        foreach ($project->getProjectInverters() as $projectInverter){
            $totalPower += ($projectInverter->getInverter()->getNominalPower());
        }

        $tnoct  = 45;
        $tc_max = 70;

        $percentPower = [];
        foreach ($project->getProjectInverters() as $projectInverter){
            $percentPower[] = $projectInverter->getInverter()->getNominalPower()  / $totalPower;
        }

        $vmax_mod = $module->getOpenCircuitVoltage();
        $vmin_mod = $module->getVoltageMaxPower() * (1 + (($tc_max - $tnoct) * ($module->getTempCoefficientVoc() / 100)));

        /**
         * @var  $key
         * @var \AppBundle\Entity\Component\ProjectInverterInterface $projectInverter
         */
        foreach (array_values($project->getProjectInverters()->toArray()) as $key => $projectInverter){
            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();

            $qte_max_mod_ser = floor($inverter->getMaxDcVoltage() / $vmax_mod);
            $qte_min_mod_ser = ceil($inverter->getMpptMin() / $vmin_mod);
            $qte_max_mod_par = floor(($inverter->getMpptMaxDcCurrent() * $inverter->getMpptNumber()) / ($module->getShortCircuitCurrent()));

            for ($p = 1; $p <= $qte_max_mod_par; $p++) {
                for ($s = $qte_min_mod_ser; $s <= $qte_max_mod_ser; $s++) {
                    $pot = ($p * $s) * ($module->getMaxPower() / 1000);
                    if ($pot >= ($power * $percentPower[$key])) {
                        $projectInverter->setSerial((int) $s);
                        $projectInverter->setParallel((int) $p);
                        break 2;
                    }
                }
            }
        }
    }
}