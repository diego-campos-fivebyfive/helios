<?php

namespace AppBundle\Entity\Component;

/**
 * Interface KitElementInterface
 */
interface KitElementInterface
{
    const TYPE_SERVICE = 1;
    const TYPE_ELEMENT = 0;

    const PRICE_STRATEGY_ABSOLUTE    = 1;
    const PRICE_STRATEGY_INCREMENTAL = 2;
    const PRICE_STRATEGY_PERCENTAGE  = 3;

    const ERROR_UNSUPPORTED_DEFINITION = 'Unsupported %s: %s';
    const ERROR_KIT_UNDEFINED = 'Undefined kit';

    /**
     * @return string
     */
    public function getToken();
    
    /**
     * @param KitInterface $kit
     * @return KitElementInterface
     */
    public function setKit(KitInterface $kit);

    /**
     * @return KitInterface
     */
    public function getKit();

    /**
     * @param $name
     * @return KitElementInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $unitPrice
     * @return KitElementInterface
     */
    public function setUnitPrice($unitPrice);

    /**
     * @return float
     */
    public function getUnitPrice();

    /**
     * @param $quantity
     * @return KitElementInterface
     */
    public function setQuantity($quantity);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return float
     */
    public function getTotalPrice();

    /**
     * @param $priceStrategy
     * @return KitElementInterface
     */
    public function setPriceStrategy($priceStrategy);

    /**
     * @return string
     */
    public function getPriceStrategy();

    /**
     * @param $rate
     * @return KitElementInterface
     */
    public function setRate($rate);

    /**
     * @return float
     */
    public function getRate();

    /**
     * @return bool
     */
    public function isElement();

    /**
     * @return bool
     */
    public function isService();

    /**
     * @return bool
     */
    public function isIncremental();

    /**
     * @return bool
     */
    public function isPrecificable();

    /**
     * @return float | null
     */
    public function getUnitPriceSale();

    /**
     * @return float | null
     */
    public function getTotalPriceSale();
    
    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getPriceStrategies();
}