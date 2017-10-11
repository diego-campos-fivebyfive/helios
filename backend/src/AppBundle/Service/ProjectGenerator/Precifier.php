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

use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\PricingManager as MarginManager;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverterInterface;
use AppBundle\Entity\Pricing\Range;
use AppBundle\Model\KitPricing;
use AppBundle\Service\Pricing\MemorialLoader;
use AppBundle\Service\Pricing\RangeLoader;

/**
 * ProjectPrecifier
 *
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */
class Precifier
{
    /**
     * @var MemorialLoader
     */
    private $memorialLoader;

    /**
     * @var RangeLoader
     */
    private $rangeLoader;

    /**
     * @var MarginManager
     */
    private $marginManager;

    /**
     * Precifier constructor.
     * @param MemorialLoader $memorialLoader
     * @param RangeLoader $rangeLoader
     * @param MarginManager $pricingManager
     */
    public function __construct(MemorialLoader $memorialLoader, RangeLoader $rangeLoader, MarginManager $marginManager)
    {
        $this->memorialLoader = $memorialLoader;
        $this->rangeLoader = $rangeLoader;
        $this->marginManager = $marginManager;
    }

    /**
     * @param ProjectInterface $project
     */
    public function priceCost(ProjectInterface $project)
    {
        if(!$project->getPower())
            $this->exception('Project power is null');

        $memorial = $this->memorialLoader->load();

        if($memorial){

            $defaults = $project->getDefaults();

            if(!array_key_exists('is_promotional', $defaults)) $defaults['is_promotional'] = false;

            $level = $defaults['is_promotional'] ? 'promotional' : $project->getLevel();

            $power = $project->getPower();
            $components = self::extractComponents($project);
            $codes = array_keys($components);
            $ranges = $this->rangeLoader->load($memorial, $power, $level, $codes);
            $costPrice = 0;

            /** @var \AppBundle\Entity\Component\ProjectElementInterface $component */
            foreach ($components as $code => $items){
                foreach ($items as $component) {
                    if(array_key_exists($component->getCode(), $ranges)) {
                        $range = $ranges[$component->getCode()];
                        $component->applyRange($range);
                        $costPrice += $component->getUnitCostPrice();
                    }
                }
            }

            /** @var \AppBundle\Entity\Component\ProjectExtraInterface $projectExtra */
            foreach ($project->getProjectExtras() as $projectExtra){

                $unitPrice = (float) $projectExtra->getExtra()->getCostPrice();

                if(1 == $projectExtra->getExtra()->getPricingby()){
                    $unitPrice = $unitPrice * $power;
                }

                $projectExtra->setUnitCostPrice($unitPrice);
                $costPrice += $unitPrice;
            }

            $project->setCostPrice($costPrice);
        }
    }

    /**
     * @param ProjectInterface $project
     */
    public function priceSale(ProjectInterface $project)
    {
        $margins = $this->marginManager->findAll();
        $percentEquipments = 0;
        $percentServices = 0;
        /** @var \AppBundle\Model\KitPricing $margin */
        foreach ($margins as $margin){
            switch ($margin->target){
                case KitPricing::TARGET_EQUIPMENTS:
                    $percentEquipments += $margin->percent;
                    break;
                case KitPricing::TARGET_SERVICES:
                    $percentServices += $margin->percent;
                    break;
                default:
                    $percentServices += $margin->percent;
                    $percentEquipments += $margin->percent;
                    break;
            }
        }

        SalePrice::calculate($project, ($percentEquipments / 100), ($percentServices / 100));
    }

    public static function extractComponents(ProjectInterface $project)
    {
        $components = [];

        foreach ($project->getProjectInverters() as $projectInverter){
            $components[$projectInverter->getInverter()->getCode()][] = $projectInverter;
        }

        foreach ($project->getProjectModules() as $projectModule){
            $components[$projectModule->getModule()->getCode()][] = $projectModule;
        }

        foreach ($project->getProjectStructures() as $projectStructure){
            $components[$projectStructure->getStructure()->getCode()][] = $projectStructure;
        }

        foreach ($project->getProjectStringBoxes() as $projectStringBox){
            $components[$projectStringBox->getStringBox()->getCode()][] = $projectStringBox;
        }

        foreach ($project->getProjectVarieties() as $projectVariety){
            $components[$projectVariety->getVariety()->getCode()][] = $projectVariety;
        }

        if(null != $transformer = $project->getTransformer()){
            $components[$transformer->getVariety()->getCode()][] = $transformer;
        }

        return $components;
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
