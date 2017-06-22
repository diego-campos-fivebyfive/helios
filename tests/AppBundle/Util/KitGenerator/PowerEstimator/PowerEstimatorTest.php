<?php

namespace Tests\AppBundle\Util\KitGenerator\PowerEstimator;

use AppBundle\Util\KitGenerator\PowerEstimator\PowerEstimator;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class PowerEstimatorTest
 * @group power_estimator
 */
class PowerEstimatorTest extends WebTestCase
{
    public function testDefaultScenario()
    {
        $consumption = 20000;
        $latitude = -15.79;
        $longitude = -47.88;
        $temperatureOperation = 45;
        $temperatureCoefficient = -0.41;
        $efficiency = 0.1711;
        $globalRadiation = [5.37,5.60,5.20,5.32,5.09,5.04,5.23,5.74,5.84,5.52,5.17,4.94];
        $airTemperature = [23.33,23.55,23.47,23.87,23.43,22.13,22.33,24.30,26.71,25.62,23.95,23.41];

        $expected = 158.23972968671;


        // with set data
        $estimator = new PowerEstimator();

        $estimator
            ->setConsumption($consumption)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setEfficiency($efficiency)
            ->setTemperatureOperation($temperatureOperation)
            ->setTemperatureCoefficient($temperatureCoefficient)
            ->setGlobalRadiation($globalRadiation)
            ->setAirTemperature($airTemperature)
        ;

        $power = $estimator->estimate();

        $this->assertEquals($expected, $power);


        // with defaults
        $estimatorDefault = new PowerEstimator();

        $estimatorDefault
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setGlobalRadiation($globalRadiation)
            ->setAirTemperature($airTemperature)
            ->setConsumption($consumption)
        ;

        $powerDefault = $estimatorDefault->estimate();

        $this->assertEquals($expected, $powerDefault);
    }
}