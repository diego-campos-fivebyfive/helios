<?php

namespace Tests\AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Service\Stock\Operation;
use Tests\AppBundle\AppTestCase;

/**
 * Class OperationTest
 * @group stock
 * @group stock_operation
 */
class OperationTest extends AppTestCase
{
    public function testCreateOperation()
    {
        $manager = $this->manager('stock_product');

        /** @var ProductInterface $product */
        $product = $manager->create();

        $product
            ->setId(uniqid(time()))
            ->setCode(uniqid(time()))
            ->setDescription('The product test')
        ;

        $amount = 500;
        $description = 'This is a transaction with amount ' . $amount;

        /** @var Operation $operation */
        $operation = Operation::create($product, $amount, $description);

        $this->assertInstanceOf(Operation::class, $operation);

        $this->assertEquals($amount, $operation->getAmount());
    }
}
