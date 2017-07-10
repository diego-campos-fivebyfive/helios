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
     * @param $markup
     * @return mixed
     */
    public function setMarkup($markup);

    /**
     * @return mixed
     */
    public function getMarkup();
}