<?php

namespace Tests\AppBundle\Service\ProjectGenerator;

use AppBundle\Service\ProjectGenerator\ShippingRuler;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group shipping_ruler
 */
class ShippingRulerTest extends WebTestCase
{
    public function testCompanyDetection()
    {
        $rule = [
            'type' => 'sices',
            'price' => 39000,
            'region' => ShippingRuler::REGION_NORTH
        ];

        ShippingRuler::apply($rule);
        $this->assertEquals('mlt', $rule['company']);

        $rule['price'] = 61000;
        ShippingRuler::apply($rule);
        $this->assertEquals('ctb', $rule['company']);

        $rule['region'] = ShippingRuler::REGION_SOUTH;
        ShippingRuler::apply($rule);
        $this->assertEquals('ctb', $rule['company']);
    }

    public function testPercentDetection()
    {
        $rule = [
            'type' => 'sices',
            'price' => 75000,
            'region' => ShippingRuler::REGION_SOUTHEAST,
            'kind' => 'interior'
        ];

        ShippingRuler::apply($rule);

        $this->assertEquals('ctb', $rule['company']);
        $this->assertEquals(100000, $rule['percent_level']);
        $this->assertEquals(3.5, $rule['percent']);
    }

    /**
     * Test default behaviors with multiple definitions
     */
    public function testDefaultRulerHandling()
    {
        /*$rule = [
            'type' => 'sices',
            'price' => 39390,
            'region' => ShippingRuler::REGION_SOUTHEAST,
            'kind' => 'interior',
            'power' => 10.45
        ];

        ShippingRuler::apply($rule);*/

        /*$this->assertEquals('ctb', $rule['company']);
        $this->assertEquals(.041, $rule['percent']);
        $this->assertEquals(.20, $rule['markup']);
        $this->assertEquals(1937.98, $rule['shipping'], '', 0.2);*/
    }
}