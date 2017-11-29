<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Service\Order\ElementResolver;
use AppBundle\Service\Order\OrderStock;
use AppBundle\Service\Stock\Identity;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class OrderStockTest
 * @group order_stock
 */
class OrderStockTest extends AppTestCase
{
    use ObjectHelperTest;

    /**
     * Component transactions via Order instance
     */
    public function testTransactElements()
    {
        $manager = $this->manager('order');

        // Add a master order
        $master = $manager->create();

        $master->setDeliveryAt(new \DateTime('5 days'));

        $order = $manager->create();
        $components = $this->createComponents(25);
        $stocks = [];

        foreach ($components as $key => $component){

            $element = new Element();

            ElementResolver::resolve($element, $component);

            // Determine identity
            $identity = Identity::create($component);
            if(!array_key_exists($identity, $stocks))
                $stocks[$identity] = 0;

            $amount = ($key + 1);

            $element->setQuantity($amount);
            $element->setOrder($order);

            $stocks[$identity] += $amount;
        }

        $master->addChildren($order);

        $manager->save($master);

        $this->service('order_reference')->generate($master);

        // Test elements is added
        $this->assertCount(count($components), $order->getElements()->toArray());

        $orderStock = $this->service('order_stock');

        // Check stock 0
        foreach ($components as $key => $component){
            $this->assertEquals(0, $component->getStock());
        }

        // Process debit
        $orderStock->debit($master);

        // Check stock updated
        foreach ($components as $key => $component){
            // Determine identity
            $identity = Identity::create($component);
            // Check stock
            $this->assertEquals(($stocks[$identity] * -1), $component->getStock());
        }

        // Process credit
        $orderStock->credit($master);

        // Check stock return to 0
        foreach ($components as $key => $component){
            $this->assertEquals(0, $component->getStock());
        }

        $products = $this->service('stock_converter')->transform($components);

        foreach ($products as $product){
            foreach ($product->getTransactions() as $transaction){
                $this->assertContains($master->getReference(), $transaction->getDescription());
            }
        }
    }

    /**
     * @param int $count
     * @return array
     */
    private function createComponents($count = 10)
    {
        $components = [];

        $families = [
            'inverter' => true,
            'module' => false,
            'stringBox' => false,
            'structure' => false,
            'variety' => false
        ];

        $fixturesNamespace = 'Tests\\AppBundle\\Entity\\DataFixtures\\Component\\%sData';

        foreach ($families as $family => $enabled){

            if($enabled) {
                $manager = $this->manager($family);
                $fixtureClass = sprintf($fixturesNamespace, ucfirst($family));

                for ($i = 0; $i < $count; $i++) {

                    $component = $manager->create();

                    $data = $fixtureClass::getData();

                    self::fluentSetters($component, $data);

                    $component->setCode(md5(uniqid(time())));

                    $manager->save($component);

                    $components[] = $component;
                }
            }
        }

        return $components;
    }
}
