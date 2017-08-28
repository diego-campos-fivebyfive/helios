<?php

namespace AppBundle\Service\PowerEstimator;

interface DataProviderInterface
{
    /**
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function getGlobalRadiation($latitude, $longitude);

    /**
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function getAirTemperature($latitude, $longitude);
}