<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

/**
 * Interface InverterInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface InverterInterface
{
    /**
     * @param $code
     * @return InverterInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $model
     * @return ComponentInterface
     */
    public function setModel($model);

    /**
     * @return string
     */
    public function getModel();

    /**
     * @param $maxDcPower
     * @return mixed
     */
    public function setMaxDcPower($maxDcPower);

    /**
     * @return mixed
     */
    public function getMaxDcPower();

    /**
     * @param $maxDcVoltage
     * @return mixed
     */
    public function setMaxDcVoltage($maxDcVoltage);

    /**
     * @return mixed
     */
    public function getMaxDcVoltage();

    /**
     * @param $nominalPower
     * @return InverterInterface
     */
    public function setNominalPower($nominalPower);

    /**
     * @return float
     */
    public function getNominalPower();

    /**
     * @param $mpptMaxDcCurrent
     * @return InverterInterface
     */
    public function setMpptMaxDcCurrent($mpptMaxDcCurrent);

    /**
     * @return float
     */
    public function getMpptMaxDcCurrent();
    
    /**
     * @param $maxEfficiency
     * @return mixed
     */
    public function setMaxEfficiency($maxEfficiency);

    /**
     * @return mixed
     */
    public function getMaxEfficiency();
    
    /**
     * @param $mpptMax
     * @return mixed
     */
    public function setMpptMax($mpptMax);

    /**
     * @return mixed
     */
    public function getMpptMax();

    /**
     * @param $mpptMin
     * @return mixed
     */
    public function setMpptMin($mpptMin);

    /**
     * @return mixed
     */
    public function getMpptMin();

    /**
     * @param $mpptNumber
     * @return mixed
     */
    public function setMpptNumber($mpptNumber);

    /**
     * @return mixed
     */
    public function getMpptNumber();

    /**
     * @param $dataSheet
     * @return ComponentInterface
     */
    public function setDataSheet($dataSheet);

    /**
     * @return string
     */
    public function getDataSheet();

    /**
     * @param $image
     * @return ComponentInterface
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param $currentPrice
     * @return InverterInterface
     */
    public function setCurrentPrice($currentPrice);

    /**
     * @return float
     */
    public function getCurrentPrice();

    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @return mixed
     */
    public function getUpdatedAt();
}