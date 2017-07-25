<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;

/**
 * Class Combiner
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Combiner
{
    /**
     * @param ProjectInterface $project
     */
    public static function combine(ProjectInterface $project)
    {
        /** @var \AppBundle\Entity\Component\ProjectModuleInterface $projectModule */
        $projectModule = $project->getProjectModules()->first();
        /** @var \AppBundle\Entity\Component\ModuleInterface $module */
        $module = $projectModule->getModule();
        $totalPower = 0;
        foreach ($project->getProjectInverters() as $projectInverter){
            $totalPower += ($projectInverter->getInverter()->getNominalPower() * $projectInverter->getQuantity());
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
        foreach ($project->getProjectInverters() as $key => $projectInverter){
            /** @var \AppBundle\Entity\Component\InverterInterface $inverter */
            $inverter = $projectInverter->getInverter();

            $qte_max_mod_ser = ceil($inverter->getMaxDcVoltage() / $vmax_mod);
            $qte_min_mod_ser = ceil($inverter->getMpptMin() / $vmin_mod);
            $qte_max_mod_par = ceil(($inverter->getMpptMaxDcCurrent() * $inverter->getMpptNumber()) / ($module->getShortCircuitCurrent()));

            for ($p = 1; $p <= $qte_max_mod_par; $p++) {
                for ($s = $qte_min_mod_ser; $s <= $qte_max_mod_ser; $s++) {
                    $pot = ($p * $s) * ($module->getMaxPower() / 1000);
                    if ($pot >= ($project->getInfPower() * $percentPower[$key])) {
                        $projectInverter->setSerial((int) $s);
                        $projectInverter->setParallel((int) $p);
                        break 2;
                    }
                }
            }
        }

        $quantity = 0;
        foreach ($project->getProjectInverters() as $projectInverter){
            $quantity += $projectInverter->getSerial() * $projectInverter->getParallel() * $projectInverter->getQuantity();
        }

        $position = $projectModule->getPosition();
        $limit = $position == 0 ? 20 : 12 ;

        $groups = [];
        if (0 != ($quantity % $limit) && ($quantity > $limit)) {

            $groups[] = [
                'lines' => ((int) floor($quantity / $limit)),
                'modules' => $limit,
                'position' => $position
            ];

            $groups[] = [
                'lines' => 1,
                'modules' => (int) ceil((($quantity / $limit) - floor($quantity / $limit)) * $limit),
                'position' => $position
            ];

        } else {

            $groups[] = [
                'lines' => ((int) ceil($quantity / $limit)),
                'modules' => (int) $quantity / ceil($quantity / $limit),
                'position' => $position
            ];
        }

        $projectModule
            ->setGroups($groups)
            ->setQuantity($quantity);
    }
}