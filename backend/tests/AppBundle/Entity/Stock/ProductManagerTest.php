<?php

namespace Tests\AppBundle\Entity\Stock;

use AppBundle\Entity\Stock\Transaction;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class ProductManagerTest
 * @group stock_product
 */
class ProductManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testCreateProductWithData()
    {
        $data = [
            'id' => self::randomString(25),
            'code' => self::randomString(10),
            'description' => self::randomString(100)
        ];

        $manager = $this->manager('stock_product');

        $product = $manager->create();

        self::fluentSettersTest($product, $data);

        $manager->save($product);

        $this->assertInstanceOf(\DateTime::class, $product->getCreatedAt());
        $this->assertCount(0, $product->getTransactions()->toArray());

        // Transactions
        $count = 10;
        $positive = 10;
        $negative = -7;
        $stock = 0;
        for ($i = 0; $i < $count; $i++){

            $transaction = new Transaction();

            $amount = 0 == $i % 2 ? $positive : $negative ;

            $transaction
                ->setAmount($amount)
                ->setDescription(self::randomString(50))
            ;

            $stock += $amount;

            $product->addTransaction($transaction);
        }

        $manager->save($product);

        $this->assertEquals($stock, $product->getStock());
        $this->assertCount(10, $product->getTransactions()->toArray());
    }
}
