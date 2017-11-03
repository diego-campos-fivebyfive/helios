<?php

namespace Tests\AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Service\Stock\Operation;
use AppBundle\Service\Stock\Provider;
use AppBundle\Service\Stock\QueryTransaction;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class QueryTransactionTest
 * @group stock
 * @group stock_query_transaction
 */
class QueryTransactionTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testTransactions()
    {
        $products = $this->createProducts();
        $product = $products[0];
        $this->createTransactions($product);

        $query = new QueryTransaction($this->getProvider());

        $query->between(
            new \DateTime('-1 month'),
            new \DateTime()
        );

        $output = $query->get('qb');
        $this->assertInstanceOf(QueryBuilder::class, $output);

        $output = $query->get('query');
        $this->assertInstanceOf(Query::class, $output);

        $output = $query->get('result');
        $this->assertArrayHasKey(0, $output);
        $this->assertEquals(count($output), $product->getTransactions()->count());


    }

    /**
     * @param int $count
     * @return array
     */
    private function createProducts($count = 10)
    {
        $products = [];
        $manager = $this->manager('stock_product');
        for($i = 0; $i < $count; $i++){

            /** @var ProductInterface $product */
            $product = $manager->create();

            $product
                ->setId(md5(uniqid(time())))
                ->setDescription(sprintf('Product %s', self::randomString(10)))
                ->setCode(self::randomString(rand(1111, 9999)))
            ;

            $manager->save($product);

            $products[] = $product;
        }

        return $products;
    }

    /**
     * Create a product transactions
     *
     * @param ProductInterface $product
     */
    private function createTransactions(ProductInterface $product)
    {
        $operations = [];
        for ($i = 0; $i < 50; $i++){

            $amount = 0 == $i % 2 ? ($i + 10) : ($i - 10);
            $operations[] = Operation::create($product, $amount, 'Test');
        }

        $this->service('stock_control')->process($operations);

        $this->assertCount(count($operations), $product->getTransactions()->toArray());

        // $manager = $this->manager('stock_product');

        /*$inc = 0;
        foreach ($product->getTransactions() as  $key => $transaction){
            if(0 == $key % 2){

                $date = new \DateTime( $inc . ' days');

                dump($date);
                $transaction->setCreatedAt($date);
                $transaction->setUpdatedAt($date);

                $inc += 10;
            }
        }*/

        // $manager->save($product);
    }

    /**
     * @return object|Provider
     */
    private function getProvider()
    {
        return $this->service('stock_provider');
    }
}
