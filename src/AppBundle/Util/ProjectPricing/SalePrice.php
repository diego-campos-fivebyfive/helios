<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Util\ProjectPricing;

use AppBundle\Entity\Component\ProjectElementInterface;
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
    public function calculate(ProjectInterface $project, $percentEquipments, $percentServices)
    {
        $costProducts = $project->getCostPriceExtraProducts();
        $costServices = $project->getCostPriceExtraServices();
        $costComponents = $project->getCostPriceComponents();
        $costEquipments = $costComponents + $costProducts;

        $saleEquipments = (100 * ($costEquipments)) / (100 - $percentEquipments);
        $saleServices = (100 * $costServices) / (100 - $percentServices);

        foreach ($project->getProjectModules() as $projectModule){
            $this->resolveUnitPriceComponent($projectModule, $costEquipments, $saleEquipments);
        }

        foreach ($project->getProjectInverters() as $projectInverter){
            $this->resolveUnitPriceComponent($projectInverter, $costEquipments, $saleEquipments);
        }

        /** @var \AppBundle\Entity\Component\ProjectItemInterface $projectExtra */
        foreach ($project->getProjectItems() as $projectExtra){

            $price = $projectExtra->getUnitCostPrice();
            $cost = $projectExtra->isProduct() ? $costEquipments : $costServices ;
            $sale = $projectExtra->isProduct() ? $saleEquipments : $saleServices ;
            $percent = $price / $cost;
            $unitSalePrice = $percent * $sale;

            $projectExtra->setUnitSalePrice($unitSalePrice);
        }

        // TODO: Calculate delivery here!
    }

    /**
     * @param ProjectElementInterface $component
     * @param $costEquipments
     * @param $saleEquipments
     */
    private function resolveUnitPriceComponent(ProjectElementInterface $component, $costEquipments, $saleEquipments)
    {
        $unitCostPrice = $component->getUnitCostPrice();
        $percent = $unitCostPrice / $costEquipments;
        $unitSalePrice = $percent * $saleEquipments;

        $component->setUnitSalePrice($unitSalePrice);
    }
}