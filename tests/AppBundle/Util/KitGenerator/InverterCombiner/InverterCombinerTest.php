<?php

namespace Tests\AppBundle\Util\KitGenerator\InverterCombiner;

use AppBundle\Util\KitGenerator\InverterCombiner\Combined;
use AppBundle\Util\KitGenerator\InverterCombiner\CombinedCollection;
use AppBundle\Util\KitGenerator\InverterCombiner\CombinedInterface;
use Liip\FunctionalTestBundle\Test\WebTestCase;

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

        /** @var CombinedInterface $combined */
        $combined = new Combined();

        foreach($data as $property => $value) {

            $setter = 'set' . ucfirst($property);
            $getter = 'get' . ucfirst($property);

            //var_dump($value); die;
            $this->assertEquals($value, $combined->$setter($value)->$getter());
        }

        $collection->addCombined($combined);
    }
}