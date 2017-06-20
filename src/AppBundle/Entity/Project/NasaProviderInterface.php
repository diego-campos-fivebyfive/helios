<?php

namespace AppBundle\Entity\Project;

use Sonata\CoreBundle\Model\ManagerInterface;

interface NasaProviderInterface extends ManagerInterface
{
    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function fromCoordinates($latitude, $longitude);

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function radiationGlobal($latitude, $longitude);

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function radiationDiffuse($latitude, $longitude);

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function radiationAtmosphere($latitude, $longitude);

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function airTemperature($latitude, $longitude);

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function airTemperatureMin($latitude, $longitude);

    /**
     * @param $latitude
     * @param $longitude
     * @return array
     */
    public function airTemperatureMax($latitude, $longitude);

    /**
     * @param $latitude
     * @return array
     */
    public function daylightHours($latitude);

    /**
     * @param $longitude
     * @return array
     */
    public function solarNoon($longitude);
}