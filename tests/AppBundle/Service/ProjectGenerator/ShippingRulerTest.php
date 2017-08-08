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
            'price' => 50000,
            'region' => ShippingRuler::REGION_MIDWEST,
            'kind' => 'interior',
            'power' => 50
        ];

        ShippingRuler::apply($rule);

        $this->assertEquals('mlt', $rule['company']);
        $this->assertEquals(.031, $rule['percent']);
        $this->assertEquals(.1, $rule['markup']);
        $this->assertEquals(1705, $rule['shipping']);
    }
}