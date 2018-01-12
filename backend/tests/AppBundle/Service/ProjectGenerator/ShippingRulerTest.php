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
            'region' => ShippingRuler::REGION_NORTH,
            'power' => 10
        ];

        ShippingRuler::apply($rule);
        $this->assertEquals('ctb', $rule['company']);

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
            'kind' => 'interior',
            'power' => 10
        ];

        ShippingRuler::apply($rule);

        $this->assertEquals(100000, $rule['percent_level']);
        $this->assertEquals(3.2, $rule['percent'], '', 1);
    }

    /**
     * Test default behaviors with multiple definitions
     */
    public function testDefaultRulerHandling()
    {
        $rule = [
            'type' => 'sices',
            'price' => 600000,
            'region' => ShippingRuler::REGION_NORTHEAST,
            'kind' => 'interior',
            'power' => 10.45
        ];

        ShippingRuler::apply($rule);

        $this->assertEquals(800000, $rule['percent_level']);
        $this->assertEquals(3.3, $rule['percent']);
    }
}
