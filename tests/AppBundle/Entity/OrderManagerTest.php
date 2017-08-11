<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Order\Element;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderManagerTest
 * @group order_manager
 */
class OrderManagerTest extends AppTestCase
{
    public function testElementsManagement()
    {
        $manager = $this->getOrderManager();

        $order = $manager->create();

        $this->assertCount(0, $order->getElements()->toArray());

        $order = $manager->create();

        $price = 100;
        $total = 0;
        for($i = 1; $i <= 5; $i++) {

            $element = new Element();
            $element
                ->setCode('ABC')
                ->setQuantity($i)
                ->setUnitPrice($price);

            $order->addElement($element);

            $total += $element->getTotal();
        }

        $this->assertEquals(5, $order->getElements()->count());
        $this->assertEquals($total, $order->getTotal());
    }

    /**
     * @return \AppBundle\Manager\OrderManager|object
     */
    private function getOrderManager()
    {
        return $this->getContainer()->get('order_manager');
    }
}