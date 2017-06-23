<?php

namespace Tests\AppBundle\Util\KitGenerator\InverterCombiner;

use AppBundle\Util\KitGenerator\InverterCombiner\Combined;
use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollection;
use AppBundle\Util\KitGenerator\InverterCombiner\CombinedInterface;
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

    public function testModuleDefault()
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

        /** @var CombinedInterface $combined */
        $combined = new Combined();
        $this->fluentSettersTest($combined, $data);

        $collection->addCombined($combined);
    }
}