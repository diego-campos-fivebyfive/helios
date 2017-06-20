<?php

namespace AppBundle\Entity\Component;

interface KitComponentInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @see model helper
     * @param MakerInterface $maker
     * @return KitComponentInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @see model helper
     * @return MakerInterface
     */
    public function getMaker();

    /**
     * @see model helper
     * @param ComponentInterface $serial
     * @return KitComponentInterface
     */
    public function setSerial(ComponentInterface $serial = null);

    /**
     * @see model helper
     * @return InverterInterface
     */
    public function getSerial();

    /**
     * @param KitInterface $kit
     * @return KitComponentInterface
     */
    public function setKit(KitInterface $kit);

    /**
     * @return KitInterface
     */
    public function getKit();

    /**
     * @param ComponentInterface $component
     * @return KitComponentInterface
     */
    public function setComponent(ComponentInterface $component);

    /**
     * @return InverterInterface | ModuleInterface | ComponentInterface
     */
    public function getComponent();

    /**
     * @param InverterInterface $inverter
     * @return KitComponentInterface
     */
    public function setInverter(InverterInterface $inverter);

    /**
     * @return InverterInterface
     */
    public function getInverter();

    /**
     * @return bool
     */
    public function isInverter();

    /**
     * @param ModuleInterface $module
     * @return KitComponentInterface
     */
    public function setModule(ModuleInterface $module);

    /**
     * @return ModuleInterface
     */
    public function getModule();

    /**
     * @return bool
     */
    public function isModule();

    /**
     * @param $quantity
     * @return KitComponentInterface
     */
    public function setQuantity($quantity);

    /**
     * @return mixed
     */
    public function getQuantity();

    /**
     * @param $price
     * @return KitComponentInterface
     */
    public function setPrice($price);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return float
     */
    public function getTotalPrice();

    /**
     * @return float | null
     */
    public function getUnitPriceSale();

    /**
     * @return float | null
     */
    public function getTotalPriceSale();

    /**
     * @return float
     */
    public function getFullPower();

    /**
     * @return float
     */
    public function getPower();

    /**
     * By default, the current price is zero,
     * use price component determine pull the price component to base price
     * @return mixed
     */
    public function usePriceComponent();

    /**
     * @return null
     */
    public function makeHelpers();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}