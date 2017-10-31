<?php

namespace Tests\AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Service\Stock\Control;
use AppBundle\Service\Stock\Operation;
use Tests\AppBundle\AppTestCase;

/**
 * Class ControlTest
 * @group stock
 * @group stock_control
 */
class ControlTest extends AppTestCase
{
    public function testDefaultProcess()
    {
        $products = $this->createProducts(10);

        /** @var Control $control */
        $control = $this->service('stock_control');

        $operations = [];
        $stocks = [];
        for ($i = 0; $i < count($products); $i++){

            $amount = (0 == $i % 2) ? 100 : -75;
            $stocks[$i] = $amount;

            $operation = Operation::create($products[$i], $amount, 'Test');

            // Via setOperations()
            $operations[] = $operation;

            // Via addOperation()
            // $control->addOperation($operation);
        }

        // Via addOperation()
        // $control->process();

        // Via setOperations()
        $control->process($operations);

        foreach ($products as $key => $product){
            $this->assertEquals($stocks[$key], $product->getStock());
            foreach ($product->getTransactions() as $transaction){
                $this->assertNotNull($transaction->getId());
            }
        }
    }

    /**
     * @param $count
     * @return array
     */
    private function createProducts($count)
    {
        $manager = $this->manager('stock_product');
        $products = [];
        for ($i=0; $i < $count; $i++){
            /** @var ProductInterface $product */
            $product = $manager->create();

            $product
                ->setId('TestProduct::' . ($i+10))
                ->setCode('CODEPRODUCT' . ($i+10))
                ->setDescription('Description Product ' . ($i+10))
            ;

            $manager->save($product, ($i + 1 == $count));

            $products[] = $product;
         }

         return $products;
    }
}
