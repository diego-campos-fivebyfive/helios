<?php

namespace Tests\AppBundle\Util\KitGenerator\InverterCombiner;

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
        /*$max  = 3;
        $cont = array_fill(0, 20, 0);
        for($j=1; $j < 20; $j++) {
            $cont[$j - 1] += 1;
            for ($k = 1; $k < $j; $k++) {
                var_dump($j);
                if ($cont[$j - $k] > $max) {
                    $cont[$j - ($k + 1)] += 1;
                    for ($z = $k; $z >= 1; $z--) {
                        $cont[$j - $z] = $cont[$j - ($z + 1)];
                    }
                }
            }
        }*/
    }

    public function testDefaultScenario()
    {
        $collection = $this->createInverterCollection();
        $combiner = new InverterCombiner();

        $combiner->combine($collection);

        var_dump($collection); die;
    }

    private function createInverterCollection()
    {
        $collection = new InverterCollection();
        foreach ($this->createInverters() as $inverter) {
            $collection->add($inverter);
        }

        return $collection;
    }

    private function createInverters()
    {
        $powers = [27.6, 33];

        $inverters = [];
        foreach($powers as $power){
            $inverter = new Inverter();
            $inverter->setNominalPower($power);
            $inverters[] = $inverter;
        }

        return $inverters;
    }
}