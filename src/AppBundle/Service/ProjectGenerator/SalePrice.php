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

use AppBundle\Entity\Component\ProjectElementInterface;
use AppBundle\Entity\Component\ProjectExtraInterface;
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
        $costProducts = $project->getCostPriceExtraProducts();
        $costServices = $project->getCostPriceExtraServices();
        $costComponents = $project->getCostPriceComponents();
        $costEquipments = $costComponents + $costProducts;

        $saleEquipments = (100 * ($costEquipments)) / (100 - $percentEquipments);
        $saleServices = (100 * $costServices) / (100 - $percentServices);

        foreach ($project->getProjectModules() as $projectModule){
            self::resolveUnitPriceComponent($projectModule, $costEquipments, $saleEquipments);
        }

        foreach ($project->getProjectInverters() as $projectInverter){
            self::resolveUnitPriceComponent($projectInverter, $costEquipments, $saleEquipments);
        }

        foreach ($project->getProjectStringBoxes() as $projectStringBox){
            self::resolveUnitPriceComponent($projectStringBox, $costEquipments, $saleEquipments);
        }

        foreach ($project->getProjectStructures() as $projectStructure){
            self::resolveUnitPriceComponent($projectStructure, $costEquipments, $saleEquipments);
        }

        foreach ($project->getProjectVarieties() as $projectVariety){
            self::resolveUnitPriceComponent($projectVariety, $costEquipments, $saleEquipments);
        }

        /** @var \AppBundle\Entity\Component\ProjectExtraInterface $projectExtra */
        foreach ($project->getProjectExtras() as $projectExtra){

            $cost = $projectExtra->isProduct() ? $costEquipments : $costServices ;
            $sale = $projectExtra->isProduct() ? $saleEquipments : $saleServices ;

            self::resolveUnitPriceExtra($projectExtra, $cost, $sale);
        }

        // TODO: Calculate delivery here!
    }

    /**
     * @param ProjectElementInterface $component
     * @param $costEquipments
     * @param $saleEquipments
     */
    private static function resolveUnitPriceComponent(ProjectElementInterface $component, $costEquipments, $saleEquipments)
    {
        $unitCostPrice = $component->getUnitCostPrice();
        $percent = $unitCostPrice / $costEquipments;
        $unitSalePrice = $percent * $saleEquipments;

        $component->setUnitSalePrice($unitSalePrice);
    }

    /**
     * @param ProjectExtraInterface $extra
     * @param $cost
     * @param $sale
     */
    private static function resolveUnitPriceExtra(ProjectExtraInterface $extra, $cost, $sale)
    {
        $unitCostPrice = $extra->getUnitCostPrice();
        $percent = $unitCostPrice / $cost;
        $unitSale = $percent * $sale;

        $extra->setUnitSalePrice($unitSale);
    }
}