<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

/**
 * Interface CombinedInterface
 * @author Daniel Martins <daniel@kolinalabs.com>
 */
interface CombinedInterface
{
    /**
     * @param $id
     * @return CombinedInterface
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $nominalPower
     * @return CombinedInterface
     */
    public function setNominalPower($nominalPower);

    /**
     * @return mixed
     */
    public function getNominalPower();

    /**
     * @param $maxDcVoltage
     * @return CombinedInterface
     */
    public function setMaxDcVoltage($maxDcVoltage);

    /**
     * @return mixed
     */
    public function getMaxDcVoltage();

    /**
     * @param $mpptMin
     * @return CombinedInterface
     */
    public function setMpptMin($mpptMin);

    /**
     * @return mixed
     */
    public function getMpptMin();

    /**
     * @param $mpptMaxDcCurrent
     * @return CombinedInterface
     */
    public function setMpptMaxDcCurrent($mpptMaxDcCurrent);

    /**
     * @return mixed
     */
    public function getMpptMaxDcCurrent();

    /**
     * @param $quantity
     * @return CombinedInterface
     */
    public function setQuantity($quantity);

    /**
     * @return mixed
     */
    public function getQuantity();

    /**
     * @param $serial
     * @return CombinedInterface
     */
    public function setSerial($serial);

    /**
     * @return mixed
     */
    public function getSerial();

    /**
     * @param $parallel
     * @return CombinedInterface
     */
    public function setParallel($parallel);

    /**
     * @return mixed
     */
    public function getParallel();


}