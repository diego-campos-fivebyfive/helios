<?php

namespace Tests\App\Generator\Common;

use App\Generator\Common\Isopleta;
use Tests\App\Generator\GeneratorTest;

/**
 * Class IsopletaTest
 * @group generator_common_isopleta
 */
class IsopletaTest extends GeneratorTest
{
    public function testIsopleta()
    {
        $tests = [
            ['long'=>-1,'lat'=>-47.5,'isopleta'=>35],
            ['long'=>-5,'lat'=>-47.5,'isopleta'=>30],
            ['long'=>-18,'lat'=>-47.5,'isopleta'=>35],
            ['long'=>-20.44,'lat'=>-47.5,'isopleta'=>35],
            ['long'=>-20.45,'lat'=>-47.5,'isopleta'=>40],
            ['long'=>-22,'lat'=>-47.5,'isopleta'=>45],
            ['long'=>-23,'lat'=>-47.5,'isopleta'=>45],
            ['long'=>-24,'lat'=>-47.5,'isopleta'=>40],
            ['long'=>-25,'lat'=>-47.5,'isopleta'=>45],
            ['long'=>-26,'lat'=>-47.5,'isopleta'=>45],
            ['long'=>-28.97,'lat'=>-47.5,'isopleta'=>45],
            ['long'=>-28.98,'lat'=>-47.5,'isopleta'=>50],
            ['long'=>-30,'lat'=>-47.5,'isopleta'=>50],
        ];
        foreach ($tests as $test) {
            $this->assertEquals($test['isopleta'], Isopleta::calculate($test['lat'], $test['long']));
        }
    }
}
