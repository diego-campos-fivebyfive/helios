<?php

namespace AppBundle\Service\PowerEstimator;

interface PowerEstimatorInterface
{
    const EFFICIENCY = 0.1711;

    const TEMPERATURE_OPERATION = 45;

    const TEMPERATURE_COEFFICIENT = -0.41;

    /**
     * @param float $kwh
     * @param float $latitude
     * @param float $longitude
     * @return float
     */
    public function estimate($kwh, $latitude, $longitude);
}