<?php

namespace Tests\AppBundle\Util\KitGenerator\InverterCombiner;

use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollection;
use AppBundle\Util\KitGenerator\InverterCombiner\CombinedInterface;
use AppBundle\Util\KitGenerator\InverterCombiner\InverterCollection;
use AppBundle\Util\KitGenerator\InverterCombiner\InverterCombiner;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Util\KitGenerator\InverterCombiner\Inverter;

/**
 * Class InverterCombinerTest
 * @group inverter_combiner
 */
class InverterCombinerTest extends WebTestCase
{
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

        foreach($data as $property => $value) {

            $setter = 'set' . ucfirst($property);
            $getter = 'set' . ucfirst($property);

            /** @var CombinedInterface $combined */
            $combined = new Combined();

            $this->assertEquals($value, $combined->$setter($value)->$getter());

            $collection->addCombined($combined);
        }
    }
}