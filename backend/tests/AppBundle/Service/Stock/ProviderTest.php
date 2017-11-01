<?php

namespace Tests\AppBundle\Service\Stock;

use Tests\AppBundle\AppTestCase;

/**
 * Class ServicePoolTest
 * @group stock
 * @group stock_provider
 */
class ProviderTest extends AppTestCase
{
    public function testProviderCacheServices()
    {
        $services = [
            'inverter_manager',
            'module_manager',
            'string_box_manager',
            'structure_manager',
            'stock_product_manager',
            'variety_manager'
        ];

        $provider = $this->service('stock_provider');

        foreach ($services as $service){
            $this->assertFalse($provider->has($service));
            $this->assertNotNull($provider->get($service));
        }

        foreach ($services as $service){
            $this->assertTrue($provider->has($service));
        }
    }

    public function testProviderCacheManagers()
    {
        $services = ['inverter', 'module', 'string_box', 'structure', 'stock_product', 'variety'];

        $provider = $this->service('stock_provider');

        foreach ($services as $service){
            $this->assertNotNull($provider->manager($service));
        }

        foreach ($services as $service){
            $id = sprintf('%s_manager', $service);
            $this->assertTrue($provider->has($id));
        }
    }
}
