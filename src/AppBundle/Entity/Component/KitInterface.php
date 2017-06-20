<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\BusinessInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface KitInterface
{
    const ERROR_UNSUPPORTED_CONTEXT = 'Unsupported context';
    const ERROR_PRICE_STRATEGY = 'Unsupported price strategy';

    const PRICE_STRATEGY_ABS = 1;
    const PRICE_STRATEGY_SUM = 2;
    const PRICE_STRATEGY_PCT = 3;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getNumber();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param $identifier
     * @return KitInterface
     */
    public function setIdentifier($identifier);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param BusinessInterface $account
     * @return KitInterface
     */
    public function setAccount(BusinessInterface $account);

    /**
     * @return BusinessInterface
     */
    public function getAccount();

    /**
     * @param KitComponentInterface $component
     * @return KitInterface
     */
    public function addComponent(KitComponentInterface $component);

    /**
     * @param KitComponentInterface $component
     * @return KitInterface
     */
    public function removeComponent(KitComponentInterface $component);

    /**
     * @return ArrayCollection
     */
    public function getComponents();

    /**
     * @param KitComponentInterface $module
     * @return KitInterface
     */
    public function addModule(KitComponentInterface $module);

    /**
     * @param KitComponentInterface $module
     * @return KitInterface
     */
    public function removeModule(KitComponentInterface $module);

    /**
     * @return ArrayCollection
     */
    public function getModules();

    /**
     * @param ModuleInterface $module
     * @return bool
     */
    public function containsModule(ModuleInterface $module);

    /**
     * @param ModuleInterface $module
     * @return KitComponentInterface
     */
    public function filterComponentModule(ModuleInterface $module);

    /**
     * @param KitComponentInterface $inverter
     * @return KitInterface
     */
    public function addInverter(KitComponentInterface $inverter);

    /**
     * @param KitComponentInterface $inverter
     * @return KitInterface
     */
    public function removeInverter(KitComponentInterface $inverter);

    /**
     * @return ArrayCollection
     */
    public function getInverters();

    /**
     * @param InverterInterface $inverter
     * @return bool
     */
    public function containsInverter(InverterInterface $inverter);

    /**
     * @param InverterInterface $inverter
     * @return KitComponentInterface
     */
    public function filterComponentInverter(InverterInterface $inverter);

    /**
     * @param ComponentInterface $component
     * @return KitComponentInterface
     */
    public function filterComponent(ComponentInterface $component);

    /**
     * @param KitElementInterface $element
     * @return KitInterface
     */
    public function addElement(KitElementInterface $element);

    /**
     * @param KitElementInterface $element
     * @return KitInterface
     */
    public function removeElement(KitElementInterface $element);

    /**
     * @return ArrayCollection
     */
    public function getElements();

    /**
     * @return ArrayCollection
     */
    public function getElementItems();

    /**
     * @return ArrayCollection
     */
    public function getElementServices();

    /**
     * @return bool
     */
    public function hasItems();

    /**
     * @return bool
     */
    public function hasServices();

    /**
     * @param $strategy
     * @return KitInterface
     */
    public function setInvoicePriceStrategy($strategy);

    /**
     * @return int
     */
    public function getInvoicePriceStrategy();

    /**
     * @param $strategy
     * @return KitInterface
     */
    public function setDeliveryPriceStrategy($strategy);

    /**
     * @return int
     */
    public function getDeliveryPriceStrategy();

    /**
     * @return float
     */
    public function getMandatoryPrice();

    /**
     * @return float
     */
    public function getTotalPriceOptionals();

    /**
     * @return float
     */
    public function getTotalPriceElements();

    /**
     * @return float
     */
    public function getTotalPriceServices();

    /**
     * @return float
     */
    public function getTotalPriceComponents();

    /**
     * @return float
     */
    public function getTotalPrice();

    /**
     * @return float
     */
    public function getFinalCost();

    /**
     * @return int
     */
    public function countModules();

    /**
     * @return int
     */
    public function countInverters();

    /**
     * @param $basePrice
     * @return KitInterface
     */
    public function setInvoiceBasePrice($basePrice);

    /**
     * @return float
     */
    public function getInvoiceBasePrice();

    /**
     * @param $basePrice
     * @return KitInterface
     */
    public function setDeliveryBasePrice($basePrice);

    /**
     * @return float
     */
    public function getDeliveryBasePrice();

    /**
     * @deprecated Use getCostOfEquipments();
     * @return float
     */
    public function getInvoicePrice();

    /**
     * @return float
     */
    public function getCostOfEquipments();

    /**
     * @return float
     */
    public function getDeliveryPrice();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return array
     */
    public static function getInvoicePriceStrategies();

    /**
     * @return array
     */
    public static function getDeliveryPriceStrategies();

    /**
     * @return null
     */
    public function prePersist();

    /**
     * @return null
     */
    public function preUpdate();

    /**
     * @return array
     */
    public function getMetadataInverters();

    /**
     * @return array
     */
    public function getMetadataModules();

    /**
     * @param array $pricingTaxes
     * @return KitInterface
     */
    public function setPricingTaxes(array $pricingTaxes);

    /**
     * @return float
     */
    public function getPricingTaxes();

    /**
     * @return float
     */
    public function getPower();

    /**
     * @return float
     */
    public function getPriceSaleEquipments();

    /**
     * @return float
     */
    public function getPriceSaleServices();

    /**
     * @return float
     */
    public function getPriceSale();

    /**
     * @param array $pricingParameters
     * @return KitInterface
     */
    public function setPricingParameters(array $pricingParameters = []);

    /**
     * @return array
     */
    public function getPricingParameters();

    /**
     * @return float
     */
    public function getTotalPercentEquipments($deep = false);

    /**
     * @return float
     */
    public function getTotalPercentServices($deep = false);

    /**
     * Return true if invoicePriceStrategy == self::PRICE_STRATEGY_SUM
     * @return bool
     */
    public function isPriceComputable();

    /**
     * @param $key
     * @param $value
     * @return KitInterface
     */
    public function addAttribute($key, $value);

    /**
     * @param $key
     * @return KitInterface
     */
    public function removeAttribute($key);

    /**
     * @param $key
     * @return bool
     */
    public function hasAttribute($key);

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getAttribute($key, $default = null);

    /**
     * @param array $attributes
     * @return KitInterface
     */
    public function setAttributes(array $attributes = []);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @return bool
     */
    public function isApplicable();
}