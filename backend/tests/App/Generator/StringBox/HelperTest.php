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

    private function stringbox_parameters($arrangement_choice)
    {
        $inputs = 0;
        for ($i = 0; $i < count($arrangement_choice); $i++) {
            $inputs += $arrangement_choice[$i]["par"];
        }
        $outputs = count($arrangement_choice);

        $parameters = [
            "inputs" => $inputs,
            "outputs" => $outputs
        ];

        return $parameters;
    }

    private function stringbox_choice($stringbox_parameters, $all_stringbox)
    {

        $inputs = $stringbox_parameters["inputs"];
        $outputs = $stringbox_parameters["outputs"];

        $selection = array();

        for ($i = 0; $i <= 10; $i++) {
            $combinations = InverterHelper::allCombinations(count($all_stringbox), $i + 1);
            $n_combinations = count($combinations);

            for ($k = 0; $k < $n_combinations; $k++) {
                $acu_in = 0;
                $acu_out = 0;
                $n_elements = count($combinations[$k]);

                for ($j = 0; $j < $n_elements; $j++) {
                    $index = $combinations[$k][$j];
                    $acu_in += $all_stringbox[$index]["in_qty"];
                    $acu_out += $all_stringbox[$index]["out_qty"];

                    $selection[$j] = $all_stringbox[$index];
                }

                $test_in = $inputs - $acu_in;
                $test_out = $outputs - $acu_out;

                if (($test_in <= 0) and ($test_out <= 0)) {
                    break 2;
                }
            }
            $selection = array();
        }
        return $selection;
    }
}
