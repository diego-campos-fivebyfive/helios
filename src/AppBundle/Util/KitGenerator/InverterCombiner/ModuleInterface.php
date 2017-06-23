<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface ModuleInterface
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
interface ModuleInterface
{

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $length
     * @return mixed
     */
    public function setLength($length);


    /**
     * @return mixed
     */
    public function getLength();


    /**
     * @param $width
     * @return mixed
     */
    public function setWidth($width);

    /**
     * @return mixed
     */
    public function getWidth();

    /**
     * @param $cellnumber
     * @return mixed
     */
    public function setCellNumber($cellNumber);


    /**
     * @return mixed
     */
    public function getCellNumber();

    /**
     * @param $openciruitvoltage
     * @return mixed
     */
    public function setOpenCircuitVoltage($openCiruitVoltage);


    /**
     * @return mixed
     */
    public function getOpenCircuitVoltage();


    /**
     * @param $voltagemaxpower
     * @return mixed
     */
    public function setVoltageMaxPower($voltageMaxPower);


    /**
     * @return mixed
     */
    public function getVoltageMaxPower();

    /**
     * @param $tempcoefficientvoc
     * @return mixed
     */
    public function setTempCoefficientVoc($tempCoefficientVoc);

    /**
     * @return mixed
     */
    public function getTempCoefficientVoc();


    /**
     * @param $maxpower
     * @return mixed
     */
    public function setMaxPower($maxPower);


    /**
     * @return mixed
     */
    public function getMaxPower();


    /**
     * @param $shortcircuitcurrent
     * @return mixed
     */
    public function setShortCircuitCurrent($shortCircuitCurrent);


    /**
     * @return mixed
     */
    public function getShortCircuitCurrent();


}