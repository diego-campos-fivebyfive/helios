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
    const DISABLE = 0;
    const ACTIVE = 1;

    /**
     * @return int
     */
    public function getId();

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
     * @return InverterInterface
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
     * @param $mpptConnections
     * @return InverterInterface
     */
    public function setMpptConnections($mpptConnections);

    /**
     * @return int
     */
    public function getMpptConnections();

    /**
     * @param $connectionType
     * @return InverterInterface
     */
    public function setConnectionType($connectionType);

    /**
     * @return string
     */
    public function getConnectionType();

    /**
     * @param $mpptParallel
     * @return InverterInterface
     */
    public function setMpptParallel($mpptParallel);

    /**
     * @return bool
     */
    public function getMpptParallel();

    /**
     * @param $inProtection
     * @return InverterInterface
     */
    public function setInProtection($inProtection);

    /**
     * @return bool
     */
    public function hasInProtection();

    /**
     * @param $phases
     * @return InverterInterface
     */
    public function setPhases($phases);

    /**
     * @return int
     */
    public function getPhases();

    /**
     * @param $datasheet
     * @return InverterInterface
     */
    public function setDatasheet($datasheet);

    /**
     * @return string
     */
    public function getDatasheet();

    /**
     * @param $image
     * @return InverterInterface
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
     * @param $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @return bool
     */
    public function isDisable();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return array
     */
    public static function getStatusOptions();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param MakerInterface $maker
     * @return InverterInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return MakerInterface
     */
    public function getMaker();
}