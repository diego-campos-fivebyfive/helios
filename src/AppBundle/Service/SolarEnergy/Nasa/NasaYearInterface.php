<?php

namespace AppBundle\Service\SolarEnergy\Nasa;

interface NasaYearInterface
{
    const LENGTH_DATA = 12;
    const SOLO_REFLECTANCE = 0.2;

    /**
     * NasaYearInterface constructor.
     * @param array $data
     */
    function __construct(array $data = []);

    /**
     * @param array $data
     * @return NasaYearInterface
     */
    public function setData(array $data);

    /**
     * @return array
     */
    public function getData();

    /**
     * @return array
     */
    public static function getSolarDeclinations();

    /**
     * @return float
     */
    public function getSoloReflectance();

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return array
     */
    public function toArray();
}