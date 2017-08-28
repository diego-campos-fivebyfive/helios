<?php

namespace Tests\AppBundle\Util\KitGenerator\InverterCombiner;

use AppBundle\Util\KitGenerator\InverterCombiner\Inverter;
use AppBundle\Util\KitGenerator\InverterCombiner\InverterCombiner;
use AppBundle\Util\KitGenerator\InverterCombiner\Module;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class InverterCombinerTest
 * @group inverter_combiner
 */
class InverterCombinerTest extends WebTestCase
{
    use ObjectHelperTest;

    public function testInverterIterator()
    {
        //$this->memorialTest();
        $module = $this->createModule();
        $inverters = $this->createInverters();

        $combiner = new InverterCombiner($inverters);

        $combiner->setModule($module);

        $data = $combiner->combine();

    }

    /*public function testModuleDefault()
    {
        $data = [
            'id' => 250,
            'length' => 75.25,
            'width' => 45,
            'cellNumber' => 60,
            'openCircuitVoltage' => 35.75,
            'voltageMaxPower' => 123,
            'tempCoefficientVoc' => -25,
            'maxPower' => 200,
            'shortCircuitCurrent' => 20.15
        ];

        $module = new Module();
        $this->fluentSettersTest($module, $data);
    }

    public function testInverterCollection()
    {
        $data = [
            'id' => 100,
            'nominalPower' => 25,
            'maxDcVoltage' => 35,
            'mpptMin' => 5,
            'mpptMaxDcCurrent' => 45,
            'quantity' => 1,
            'serial' => 3,
            'parallel' => 4,
        ];

        $collection = new CombinedCollection();

        /** @var CombinedInterface $combined *
        $combined = new Combined();
        $this->fluentSettersTest($combined, $data);

        $collection->addCombined($combined);
    }*/

    /**
     * @return array
     */
    private function createInverters()
    {
        $inverters = [];

        $data = [
            ['id' => 5830, 'name' => 'AGILO 75.0-3 1', 'nominalPower' => 75],
            ['id' => 5831, 'name' => 'AGILO 75.0-3 2', 'nominalPower' => 75],
            ['id' => 5828, 'name' => 'AGILO 75.0-3 3', 'nominalPower' => 100],
            ['id' => 5829, 'name' => 'AGILO 75.0-3 4', 'nominalPower' => 100],
        ];

        foreach($data as $config){
            $inverter = new Inverter();
            $this->fluentSettersTest($inverter, $config);
            $inverters[] = $inverter;
        }

        return $inverters;
    }

    /**
     * @return Module
     */
    private function createModule()
    {
        $data = [
            'id' => 32433,
            'length' => 1.65,
            'width' => 0.992,
            'cellNumber' => 60,
            'openCircuitVoltage' => 38.5,
            'voltageMaxPower' => 31.5,
            'tempCoefficientVoc' => -0.41,
            'maxPower' => 229.44760804573,
            'shortCircuitCurrent' => 9.43
        ];

        $module = new Module();

        $this->fluentSettersTest($module, $data);

        return $module;
    }

    private function memorialTest()
    {
        $randomMemorial = function(){
            return [
                'platinum' => [
                    '1000-5000' => [
                        'icms' => 0.1,
                        'pis' => 0.05,
                        'cofins' => 0.07
                    ],
                    '5001-10000' => [
                        'icms' => 0.1,
                        'pis' => 0.05,
                        'cofins' => 0.07
                    ],
                    '10001-15000' => [
                        'icms' => 0.1,
                        'pis' => 0.05,
                        'cofins' => 0.07
                    ],
                ],
                'gold' => [
                    '1000-5000' => [
                        'icms' => 0.08,
                        'pis' => 0.04,
                        'cofins' => 0.03
                    ],
                    '5001-10000' => [
                        'icms' => 0.3,
                        'pis' => 0.4,
                        'cofins' => 0.09
                    ],
                    '10001-15000' => [
                        'icms' => 0.2,
                        'pis' => 0.05,
                        'cofins' => 0.05
                    ],
                ]

            ];
        };

        $data = [
            'family' => 'inverter',
            'code' => substr(strtoupper(uniqid(time())), 0, 8),
            'description' => 'This is a description',
            'memorial' => $randomMemorial()
        ];

        echo  json_encode($data); die;
    }
}