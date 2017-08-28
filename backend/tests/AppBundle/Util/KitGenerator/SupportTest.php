<?php

namespace Tests\AppBundle\Util\KitGenerator;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Util\KitGenerator\Support;

/**
 * Class SupportTest
 * @group kit_generator
 */
class SupportTest extends WebTestCase
{

    public function testFactorial()
    {
        $this->assertEquals(120, Support::factorial(5));
        $this->assertEquals(1, Support::factorial(0));
        $this->assertEquals(1, Support::factorial(-5));
        $this->assertEquals(-1, Support::sign(-10)); //primeiro o numero que deve ser o resultado, depois o numero plo qual percorre o algorithimo
        $this->assertEquals(1, Support::combine(1,2));
    }


}