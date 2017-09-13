<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 10/07/17
 * Time: 12:15
 */

namespace AppBundle\Entity\Pricing;


interface RangeInterface
{
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
     * @param $price
     * @return mixed
     */
    public function setPrice($price);

    /**
     * @return mixed
     */
    public function getPrice();

    /**
     * @param $memorial
     * @return mixed
     */
    public function setMemorial(MemorialInterface $memorial);

    /**
     * @return mixed
     */
    public function getMemorial();
}
