<?php

namespace Tests\App\Generator\StringBox;

use App\Generator\StringBox\Helper;
use App\Generator\Inverter\Helper as InverterHelper;
use Tests\App\Generator\GeneratorTest;

/**
 * Class HelperTest
 * @group generator_stringbox_helper
 */
class HelperTest extends GeneratorTest
{
    public function testStringboxParameters()
    {
        $choices = [
            ["par" => 6],
            ["par" => 4],
            ["par" => 2]
        ];

        //$parameter = $this->stringbox_parameters($choices);
        $parameter = Helper::getParameters($choices);
        //print_r($parameter);die;

        self::assertCount(2, $parameter);
        self::assertArrayHasKey('inputs', $parameter);
    }

    public function testStringboxChoice()
    {
        $parameters = [
            "inputs" => 4,
            "outputs" => 2
        ];

        $stringbox = [
            ["in_qty" => 4, "out_qty" => 0],
            ["in_qty" => 2, "out_qty" => 0],
            ["in_qty" => 1.2, "out_qty" => 0.7],
            ["in_qty" => 1, "out_qty" => 0.3]
        ];

        $choices = Helper::getChoices($parameters, $stringbox);

        //print_r($choices);die;

        self::assertCount(4, $choices);
        self::assertCount(2, $choices[2]);
    }
}
