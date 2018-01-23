<?php

namespace Tests\App\Generator\Inverter;

use App\Generator\Inverter\Helper;
use Tests\App\Generator\GeneratorTest;

/**
 * Class HelperTest
 * @group generator_inverter_helper
 */
class HelperTest extends GeneratorTest
{
    /**
     * Test power is adjusted
     */
    public function testAdjustPower()
    {
        $inverters = [
            ['nominal_power' => 4],
            ['nominal_power' => 5],
            ['nominal_power' => 6],
            ['nominal_power' => 7],
        ];

        $desiredPower = 0;
        $fdiMax = 1.12;

        $this->assertGreaterThan(0, Helper::adjustPower($inverters, $desiredPower, $fdiMax));
    }

}
