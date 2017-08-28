<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectElementTrait;
use AppBundle\Entity\Component\ProjectInterface;

/**
 * Class SalePrice
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class SalePrice
{
    /**
     * @param ProjectInterface $project
     * @param $percentEquipments
     * @param $percentServices
     */
    public static function calculate(ProjectInterface $project, $percentEquipments, $percentServices)
    {
        foreach ($project->getProjectModules() as $projectModule){
            self::resolveUnitPriceComponent($projectModule, $percentEquipments);
        }

        foreach ($project->getProjectInverters() as $projectInverter){
            self::resolveUnitPriceComponent($projectInverter, $percentEquipments);
        }

        foreach ($project->getProjectStringBoxes() as $projectStringBox){
            self::resolveUnitPriceComponent($projectStringBox, $percentEquipments);
        }

        foreach ($project->getProjectStructures() as $projectStructure){
            self::resolveUnitPriceComponent($projectStructure, $percentEquipments);
        }

        foreach ($project->getProjectVarieties() as $projectVariety){
            self::resolveUnitPriceComponent($projectVariety, $percentEquipments);
        }

        if(null != $transformer = $project->getTransformer()){
            self::resolveUnitPriceComponent($transformer, $percentEquipments);
        }

        foreach ($project->getProjectExtras() as $projectExtra){

            $percent = $projectExtra->isProduct() ? $percentEquipments : $percentServices ;

            self::resolveUnitPriceComponent($projectExtra, $percent);
        }
    }

    /**
     * @param ProjectElementTrait $component
     * @param $percent
     */
    private static function resolveUnitPriceComponent($component, $percent)
    {
        $unitSalePrice = $component->getUnitCostPrice() / (1 - $percent);
        $component->setUnitSalePrice($unitSalePrice);
    }
}