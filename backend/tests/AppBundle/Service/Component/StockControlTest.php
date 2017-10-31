<?php

namespace Tests\AppBundle\Service\Component;

use AppBundle\Service\Component\StockControl;
use AppBundle\Service\Stock\Converter;
use AppBundle\Service\Stock\Provider;
use Tests\AppBundle\AppTestCase;

/**
 * Class StockControlTest
 * @group component_stock
 */
class StockControlTest extends AppTestCase
{
    public function testDebitProcess()
    {
        $module = $this->getFixture('module');

        $this->assertEquals(0, $module->getStock());

        $stockControl = $this->service('stock_control');
        //$componentStock = $this->service('component_stock');
        $componentStock = new StockControl($this->getContainer());
        $converter = $this->getConverter();

        $product = $converter->transform($module);
        $total = 0;
        for($i = 0; $i < 10; $i++){
            $amount = $i + 1;
            $stockControl->addOperation($product, $amount, 'Test');
            $total += $amount;
        }

        $stockControl->process();

        $componentStock->update([$module]);

        $this->assertEquals($total, $module->getStock());
    }

    /**
     * @return Converter
     */
    private function getConverter()
    {
        /** @var Provider $provider */
        $provider = $this->service('stock_provider');

        return new Converter($provider);
    }
}
