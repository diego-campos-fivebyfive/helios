<?php

namespace AppBundle\Entity\Component;


use AppBundle\Entity\AccountInterface;

interface ExtraInterface
{
    const TYPE_PRODUCT = 0;
    const TYPE_SERVICE = 1;

    const PRICING_FIXED = 0;
    const PRICING_POWER = 1;

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $pricingBy
     * @return mixed
     */
    public function setPricingBy($pricingBy);

    /**
     * @return mixed
     */
    public function getPricingBy();

    /**
     * @param $costPrice
     * @return mixed
     */
    public function setCostPrice($costPrice);

    /**
     * @return mixed
     */
    public function getCostPrice();

    /**
     * @return bool
     */
    public function isService();

    /**
     * @return bool
     */
    public function isProduct();

    /**
     * @param AccountInterface $account
     * @return ExtraInterface
     */
    public function setAccount(AccountInterface $account);

    /**
     * @return AccountInterface
     */
    public function getAccount();

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getPricingOptions();

    /**
     * @return array
     */
    public function getLabel();
}