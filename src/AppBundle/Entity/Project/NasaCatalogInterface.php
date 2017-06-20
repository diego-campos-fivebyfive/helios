<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\BusinessInterface;

interface NasaCatalogInterface
{
    const AIR_TEMPERATURE = 'air_temperature';
    const AIR_TEMPERATURE_MIN = 'air_temperature_min';
    const AIR_TEMPERATURE_MAX = 'air_temperature_max';
    const DAYLIGHT_HOURS = 'daylight_hours';
    const RADIATION_DIFFUSE = 'radiation_diffuse';
    const RADIATION_GLOBAL  = 'radiation_global';
    const RADIATION_ATMOSPHERE = 'radiation_atmosphere';
    const SOLAR_NOON = 'solar_noon';

    const ERROR_UNSUPPORTED_CONTEXT = 'Unsupported context';

    /**
     * @param BusinessInterface $account
     * @return NasaCatalogInterface
     */
    public function setAccount(BusinessInterface $account);

    /**
     * @return BusinessInterface
     */
    public function getAccount();

    /**
     * @return string
     */
    public function getContext();

    /**
     * @return array
     */
    public function getMonths();

    /**
     * @return float
     */
    public function getLatitude();

    /**
     * @return float
     */
    public function getLongitude();

    /**
     * @return array
     */
    public function getContextList();

    /**
     * @return array
     */
    public function toArray();
}