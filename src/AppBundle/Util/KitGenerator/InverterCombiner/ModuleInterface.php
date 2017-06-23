<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 23/06/17
 * Time: 13:54
 */

namespace AppBundle\Util\KitGenerator\InverterCombiner;


/**
 * Interface ModuleInterface
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
interface ModuleInterface
{

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
    public function setCellNumber($cellnumber);


    /**
     * @return mixed
     */
    public function getCellNumber();

    /**
     * @param $openciruitvoltage
     * @return mixed
     */
    public function setOpenCircuitVoltage($openciruitvoltage);


    /**
     * @return mixed
     */
    public function getOpenCircuitVoltage();


    /**
     * @param $voltagemaxpower
     * @return mixed
     */
    public function setVoltageMaxPower($voltagemaxpower);


    /**
     * @return mixed
     */
    public function getVoltageMaxPower();

    /**
     * @param $tempcoefficientvoc
     * @return mixed
     */
    public function setTempCoefficientVoc($tempcoefficientvoc);

    /**
     * @return mixed
     */
    public function getTempCoefficientVoc();


    /**
     * @param $maxpower
     * @return mixed
     */
    public function setMaxPower($maxpower);


    /**
     * @return mixed
     */
    public function getMaxPower();


    /**
     * @param $shortcircuitcurrent
     * @return mixed
     */
    public function setShortCircuitCurrent($shortcircuitcurrent);


    /**
     * @return mixed
     */
    public function getShortCircuitCurrent();



}