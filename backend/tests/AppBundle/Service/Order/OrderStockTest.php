<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Service\Order\ElementResolver;
use AppBundle\Service\Order\OrderStock;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class OrderStockTest
 * @group order_stock
 */
class OrderStockTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDebitElements()
    {
        $manager = $this->manager('order');
        $order = $manager->create();
        $components = $this->createComponents();

        foreach ($components as $component){

            $element = new Element();

            ElementResolver::resolve($element, $component);

            $element->setOrder($order);
        }

        // Test elements is added
        $this->assertCount(count($components), $order->getElements()->toArray());

        //$orderStock = $this->service('order_stock');
        $orderStock = new OrderStock(
            $this->service('component_collector'),
            $this->service('stock_converter')
        );

        $orderStock->debit($order);
    }

    /**
     * @param int $count
     * @return array
     */
    private function createComponents($count = 10)
    {
        $components = [];
        $families = ['inverter', 'module', 'stringBox', 'structure', 'variety'];

        $fixturesNamespace = 'Tests\\AppBundle\\Entity\\DataFixtures\\Component\\%sData';

        foreach ($families as $family){

            $manager = $this->manager($family);
            $fixtureClass = sprintf($fixturesNamespace, ucfirst($family));

            for ($i = 0; $i < $count; $i++) {

                $component = $manager->create();

                $data = $fixtureClass::getData();

                self::fluentSetters($component, $data);

                $manager->save($component);

                $components[] = $component;
            }
        }

        return $components;
    }
}
