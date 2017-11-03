<?php

namespace Tests\AppBundle\Service\Stock;

use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Entity\Stock\Transaction;
use AppBundle\Service\Stock\Operation;
use AppBundle\Service\Stock\Provider;
use AppBundle\Service\Stock\Query;
use Doctrine\ORM\Query as DoctrineQuery;
use Doctrine\ORM\QueryBuilder;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class QueryTest
 * @group stock
 * @group stock_query
 */
class QueryTest extends AppTestCase
{
    use ObjectHelperTest;

    /**
     * Test return types
     */
    public function testReturnInstancesOrTypes()
    {
        $products = $this->createProducts();
        $product = $products[0];

        $this->createTransactions($product, 25);

        $query = new Query($this->getProvider());

        $output = $query->qb();
        $this->assertInstanceOf(QueryBuilder::class, $output);

        $output = $query->query();
        $this->assertInstanceOf(DoctrineQuery::class, $output);

        $output = $query->result();
        $this->assertArrayHasKey(0, $output);
        $this->assertEquals(count($output), $product->getTransactions()->count());

        $output = $query->pagination();
        $this->assertInstanceOf(SlidingPagination::class, $output);

        $output = $query->count();
        $this->assertEquals(25, $output);
    }

    /**
     * Test return based product
     */
    public function testReturnByProduct()
    {
        $products = $this->createProducts();

        $count = 20;
        $product = $products[0];
        $productWithoutTransactions = $products[1];

        $this->createTransactions($product, $count);

        $query = new Query($this->getProvider());

        $query->product($product);
        $this->assertCount($count, $query->get('result'));

        $query->product($productWithoutTransactions);
        $this->assertCount(0, $query->get('result'));
    }

    /**
     * Test return based transaction dates
     */
    public function testReturnByDateIntervals()
    {
        $products = $this->createProducts();

        $count = 10;
        $product = $products[0];

        $this->createTransactions($product, $count);

        $manager = $this->manager('stock_product');

        $connection = $manager->getConnection();
        $table = $manager->getObjectManager()->getClassMetadata(Transaction::class)->table['name'];

        $date = new \DateTime('-1 month');
        $days = 2;

        /** @var Transaction $transaction */
        foreach ($product->getTransactions() as $key => $transaction){
            if(0 == $key % 2) {

                $interval = new \DateInterval(sprintf('P%sD', $days));
                $date->add($interval);

                $dateFormat = $date->format('Y-m-d H:i:s');

                $connection->update(
                    $table,
                    ['created_at' => $dateFormat, 'updated_at' => $dateFormat],
                    ['id' => $transaction->getId()]
                );
            }
        }

        $query = new Query($this->getProvider());

        $query->between(
            new \DateTime('-2 days'),
            new \DateTime('-1 day')
        );

        $this->assertCount(0, $query->get('result'));

        $query->between(
            new \DateTime('-1 day'),
            new \DateTime()
        );

        $this->assertCount($count / 2, $query->get('result'));
    }

    /**
     * Test return based transaction like search
     */
    public function testReturnBySearch()
    {
        $this->markTestSkipped('Like filter is processed by paginator!');

        $products = $this->createProducts(2);

        $product = $products[0];

        $operations = [
            Operation::create($product, 100, 'Foo Transaction'),
            Operation::create($product, 100, 'Bar Transaction'),
            Operation::create($product, 100, 'Foo Bar'),
            Operation::create($product, 100, 'Foo'),
            Operation::create($product, 100, 'Bar'),
            Operation::create($product, 100, 'Transaction'),
        ];

        $this->service('stock_control')->process($operations);

        $this->assertCount(count($operations), $product->getTransactions()->toArray());

        /** @var Query $query */
        $query = $this->service('stock_query');

        $query->like('transact');
        $this->assertCount(3, $query->result());

        $query->like('foo');
        $this->assertCount(3, $query->result());
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
    private function createTransactions(ProductInterface $product, $count = 50)
    {
        $operations = [];
        for ($i = 0; $i < $count; $i++){

            $amount = 0 == $i % 2 ? ($i + 10) : ($i - 10);
            $operations[] = Operation::create($product, $amount, 'Test');
        }

        $this->service('stock_control')->process($operations);
    }

    /**
     * @return object|Provider
     */
    private function getProvider()
    {
        return $this->service('stock_provider');
    }
}
