<?php

namespace AppBundle\Entity\Pricing;


interface RangeInterface
{
    const DEFAULT_TAX = 0.0925;

    /**
     * @param $code
     * @return mixed
     */
    public function setCode($code);

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @param $initialPower
     * @return mixed
     */
    public function setInitialPower($initialPower);

    /**
     * @return mixed
     */
    public function getInitialPower();

    /**
     * @param $finalPower
     * @return mixed
     */
    public function setFinalPower($finalPower);

    /**
     * @return mixed
     */
    public function getFinalPower();

    /**
     * @param $level
     * @return mixed
     */
    public function setLevel($level);

    /**
     * @return mixed
     */
    public function getLevel();

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
     * @param $price
     * @return mixed
     */
    public function setPrice($price);

    /**
     * @return mixed
     */
    public function getPrice();

    /**
     * @param float $tax
     * @return RangeInterface
     */
    public function setTax($tax);

    /**
     * @return float
     */
    public function getTax();

    /**
     * @param float $markup
     * @return RangeInterface
     */
    public function setMarkup($markup);

    /**
     * @return float
     */
    public function getMarkup();

    /**
     * @param $memorial
     * @return mixed
     */
    public function setMemorial(MemorialInterface $memorial);

    /**
     * @return mixed
     */
    public function getMemorial();

    /**
     * @param $code
     * @param $level
     * @param $initialPower
     * @param $finalPower
     * @return bool
     */
    public function hasConfig($code, $level, $initialPower, $finalPower);
}
