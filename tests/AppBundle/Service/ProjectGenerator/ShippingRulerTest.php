<?php

namespace Tests\AppBundle\Service\ProjectGenerator;

use AppBundle\Service\ProjectGenerator\ShippingRuler;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group shipping_ruler
 */
class ShippingRulerTest extends WebTestCase
{
    /**
     * Test default behaviors with multiple definitions
     */
    public function testDefaultRulerHandling()
    {
        $rule = [
            'type' => 'sices',
            'price' => 61000,
            'region' => ShippingRuler::REGION_NORTH,
            'kind' => 'capital',
            'power' => 12
        ];

        ShippingRuler::apply($rule);

        $this->assertEquals('ctp', $rule['company']);
        $this->assertEquals(.047, $rule['percent']);
        $this->assertEquals(.20, $rule['markup']);
        $this->assertEquals(3440.40, $rule['shipping']);
    }
}