<?php

namespace AppBundle\Service\ProjectGenerator;

abstract class AbstractConfig
{
    public static $maxInverters = 15;

    const PHASE_NUMBER_MONOPHASIC = 'Monophasic';
    const PHASE_NUMBER_BIPHASIC = 'Biphasic';
    const PHASE_NUMBER_TRIPHASIC = 'Triphasic';

    /**
     * @return array
     */
    public static function getPhaseNumbers()
    {
        return [
            self::PHASE_NUMBER_MONOPHASIC,
            self::PHASE_NUMBER_BIPHASIC,
            self::PHASE_NUMBER_TRIPHASIC
        ];
    }

    /**
     * @return array
     */
    public static function getVoltages()
    {
        return ['127/220', '220/380'];
    }
}