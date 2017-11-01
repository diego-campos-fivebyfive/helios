<?php

namespace Tests\AppBundle\Service\Component;

use AppBundle\Service\Stock\Component;
use Tests\AppBundle\AppTestCase;

/**
 * Class ComponentTest
 * @group stock_component
 */
class ComponentTest extends AppTestCase
{
    public function testTransactProcess()
    {
        $module = $this->getFixture('module');

        $this->assertEquals(0, $module->getStock());

        /** @var Component $stockComponent */
        $stockComponent = $this->service('stock_component');

        $transactions = [
            ['component' => $module, 'amount' => 100, 'description' => 'Test'],
            ['component' => $module, 'amount' => 300, 'description' => 'Test']
        ];

        $stockComponent->transact($transactions);

        $this->assertEquals(400, $module->getStock());
    }
}
