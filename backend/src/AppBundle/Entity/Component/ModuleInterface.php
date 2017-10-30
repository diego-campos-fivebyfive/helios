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
 * Interface ModuleInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ModuleInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $code
     * @return ModuleInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $model
     * @return ModuleInterface
     */
    public function setModel($model);

    /**
     * @return string
     */
    public function getModel();

    /**
     * @param $description
     * @return ModuleInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $cellNumber
     * @return ModuleInterface
     */
    public function setCellNumber($cellNumber);

    /**
     * @return int
     */
    public function getCellNumber();

    /**
     * @param $maxPower
     * @return ModuleInterface
     */
    public function setMaxPower($maxPower);

    /**
     * @return float
     */
    public function getMaxPower();

    /**
     * @param $voltageMaxPower
     * @return ModuleInterface
     */
    public function setVoltageMaxPower($voltageMaxPower);

    /**
     * @return float
     */
    public function getVoltageMaxPower();

    /**
     * @param $currentMaxPower
     * @return ModuleInterface
     */
    public function setCurrentMaxPower($currentMaxPower);

    /**
     * @return float
     */
    public function getCurrentMaxPower();

    /**
     * @param $openCircuitVoltage
     * @return ModuleInterface
     */
    public function setOpenCircuitVoltage($openCircuitVoltage);

    /**
     * @return float
     */
    public function getOpenCircuitVoltage();

    /**
     * @param $shortCircuitCurrent
     * @return ModuleInterface
     */
    public function setShortCircuitCurrent($shortCircuitCurrent);

    /**
     * @return float
     */
    public function getShortCircuitCurrent();

    /**
     * @param $efficiency
     * @return ModuleInterface
     */
    public function setEfficiency($efficiency);

    /**
     * @return float
     */
    public function getEfficiency();

    /**
     * @param $temperatureOperation
     * @return ModuleInterface
     */
    public function setTemperatureOperation($temperatureOperation);

    /**
     * @return int
     */
    public function getTemperatureOperation();

    /**
     * @param $tempCoefficientMaxPower
     * @return ModuleInterface
     */
    public function setTempCoefficientMaxPower($tempCoefficientMaxPower);

    /**
     * @return float
     */
    public function getTempCoefficientMaxPower();

    /**
     * @deprecated use self::setTempCoefficientIsc()
     * @param $tempCoefficientShortCircuitCurrent
     * @return ModuleInterface
     */
    public function setTempCoefficientShortCircuitCurrent($tempCoefficientShortCircuitCurrent);

    /**
     * @deprecated use self::getTempCoefficientIsc()
     * @return float
     */
    public function getTempCoefficientShortCircuitCurrent();

    /**
     * @param $tempCoefficientIsc
     * @return ModuleInterface
     */
    public function setTempCoefficientIsc($tempCoefficientIsc);

    /**
     * @return float
     */
    public function getTempCoefficientIsc();

    /**
     * @deprecated use self::setTempCoefficientVoc()
     * @param $tempCoefficientOpenCircuitVoltage
     * @return ModuleInterface
     */
    public function setTempCoefficientOpenCircuitVoltage($tempCoefficientOpenCircuitVoltage);

    /**
     * @deprecated use self::getTempCoefficientVoc()
     * @return float
     */
    public function getTempCoefficientOpenCircuitVoltage();

    /**
     * @param $tempCoefficientVoc
     * @return ModuleInterface
     */
    public function setTempCoefficientVoc($tempCoefficientVoc);

    /**
     * @return float
     */
    public function getTempCoefficientVoc();

    /**
     * @param $length
     * @return ModuleInterface
     */
    public function setLength($length);

    /**
     * @return float
     */
    public function getLength();

    /**
     * @param $width
     * @return ModuleInterface
     */
    public function setWidth($width);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @param $height
     * @return ModuleInterface
     */
    public function setHeight($height);

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @param $cellType
     * @return ModuleInterface
     */
    public function setCellType($cellType);

    /**
     * @return string
     */
    public function getCellType();

    /**
     * @param $connectionType
     * @return ModuleInterface
     */
    public function setConnectionType($connectionType);

    /**
     * @return string
     */
    public function getConnectionType();

    /**
     * @param $datasheet
     * @return ModuleInterface
     */
    public function setDatasheet($datasheet);

    /**
     * @return string
     */
    public function getDatasheet();

    /**
     * @param $image
     * @return ModuleInterface
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param $currentPrice
     * @return ModuleInterface
     */
    public function setCurrentPrice($currentPrice);

    /**
     * @return float
     */
    public function getCurrentPrice();

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
     * @return ModuleInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return MakerInterface
     */
    public function getMaker();
}
