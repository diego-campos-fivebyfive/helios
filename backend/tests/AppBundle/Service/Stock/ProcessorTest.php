<?php

namespace Tests\AppBundle\Service\Stock;

use AppBundle\Service\Stock\Identity;
use Tests\AppBundle\AppTestCase;

/**
 * Class ProcessorTest
 * @group stock
 * @group stock_processor
 */
class ProcessorTest extends AppTestCase
{
    public function testSingleProcess()
    {
        $module = $this->getFixture('module');
        $processor = $this->service('stock_processor');

        $processor->input($module, 100);

        $id = Identity::create($module);
        $product = $this->manager('stock_product')->find($id);

        $this->assertEquals(1, $product->getTransactions()->toArray());
    }
}
