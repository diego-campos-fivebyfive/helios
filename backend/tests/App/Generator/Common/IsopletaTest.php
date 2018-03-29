<?php

namespace Tests\App\Generator\Common;

use App\Generator\Common\Math;
use Tests\App\Generator\GeneratorTest;

/**
 * Class MathTest
 * @group generator_common_math
 */
class MathTest extends GeneratorTest
{
    /**
     * Test factorial functionality
     * @see http://blogcalculadora.blogspot.com.br/2012/08/tabuada-tabela-de-fatorial-de-1-100.html
     */
    public function testFactorial()
    {
        $this->assertEquals(1, Math::factorial(0));
        $this->assertEquals(5040, Math::factorial(7));
    }

    /**
     * Test combinations functionality (with repetition)
     * @see http://www.centralexatas.com.br/matematica/analise-combinatoria/32
     */
    public function testCombinations()
    {
        //print_r(Math::combinations(2, 2) . "\n"); die;
        //$this->assertEquals(1, Math::factorial(0));
        //$this->assertEquals(5040, Math::factorial(7));
    }
}
