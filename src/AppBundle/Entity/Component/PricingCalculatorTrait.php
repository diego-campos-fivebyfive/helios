<?php

namespace AppBundle\Entity\Component;

use AppBundle\Model\KitPricing;

/**
 * Class PricingCalculatorTrait
 * @package AppBundle\Entity\Component
 */
trait PricingCalculatorTrait
{
    private $pricingParameters = [];

    private $percentEquipments = 0;
    private $percentServices = 0;
    private $percentGeneral = 0;

    private $priceSaleEquipments = 0;
    private $priceSaleServices = 0;
    private $priceSale = 0;

    /**
     * @param array $pricingParameters
     */
    public function setPricingParameters(array $pricingParameters = [])
    {
        $this->pricingParameters = $pricingParameters;
        $this->update();
    }

    /**
     * @return array
     */
    public function getPricingParameters()
    {
        return $this->pricingParameters;
    }

    /**
     * @param bool $deep
     * @return int
     */
    public function getPercentEquipments($deep = false)
    {
        return $deep ? $this->percentEquipments : $this->percentEquipments / 100;
    }

    /**
     * @param bool $deep
     * @return float|int
     */
    public function getTotalPercentEquipments($deep = false)
    {
        $percent = $this->percentEquipments + $this->percentGeneral;

        return (float) $deep ? $percent : $percent / 100;
    }

    /**
     * @param bool $deep
     * @return float|int
     */
    public function getPercentServices($deep = false)
    {
        return $deep ? $this->percentServices : $this->percentServices / 100;
    }

    /**
     * @param bool $deep
     * @return float|int
     */
    public function getTotalPercentServices($deep = false)
    {
        $percent = $this->percentServices + $this->percentGeneral;

        return (float) $deep ? $percent : $percent / 100;
    }

    /**
     * @param bool $deep
     * @return float|int
     */
    public function getPercentGeneral($deep = false)
    {
        return (float) $deep ? $this->percentGeneral : $this->percentGeneral / 100;
    }

    /**
     * @inheritDoc
     */
    public function getPriceSaleEquipments()
    {
        return (float) $this->priceSaleEquipments;
    }

    /**
     * @inheritDoc
     */
    public function getPriceSaleServices()
    {
        return (float) $this->priceSaleServices;
    }

    /**
     * @inheritDoc
     */
    public function getPriceSale()
    {
        return $this->priceSale;
    }
    
    /**
     * Update all data based parameters
     */
    private function update()
    {
        foreach($this->pricingParameters as $pricingParameter) {
            if(!$pricingParameter instanceof KitPricing){
                throw new \InvalidArgumentException('Invalid parameter instance');
            }

            $pricingParameter->kit = $this;

            $percent = (float) $pricingParameter->percent;

            switch($pricingParameter->target){
                case KitPricing::TARGET_EQUIPMENTS:
                    $this->percentEquipments += $percent;
                    break;
                case KitPricing::TARGET_SERVICES:
                    $this->percentServices += $percent;
                    break;
                case KitPricing::TARGET_GENERAL:
                    $this->percentGeneral += $percent;
                    break;
            }
        }

        $this->calculatePrices();
    }


    /**
     * Calculate price sales
     */
    private function calculatePrices()
    {
        $finalCost = $this->getFinalCost();

        $this->priceSaleEquipments = (100 * $finalCost) / (100-($this->getTotalPercentEquipments(true)));

        $this->priceSaleServices = (100 * $this->getTotalPriceServices()) / (100-($this->getTotalPercentServices(true)));

        $this->priceSale = $this->getPriceSaleEquipments() + $this->getPriceSaleServices();
    }
}