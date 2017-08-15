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

    public function testSelfAssociations()
    {
        $manager = $this->getOrderManager();

        $order1 = $manager->create();
        $order2 = $manager->create();

        $this->assertFalse($order1->isBudget());

        $order1->addChildren($order2);
        $this->assertTrue($order1->isBudget());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSelfAssociationException()
    {
        $manager = $this->getOrderManager();

        $order1 = $manager->create();
        $order2 = $manager->create();
        $order3 = $manager->create();

        $order2->setParent($order1);
        $this->assertTrue($order1->isBudget());

        // TODO: Uncomment to run all tests
        //$order3->addChildren($order1);    via addChildren(parent)
        //$order2->addChildren($order3);    via children::addChildren
        //$order1->setParent($order3);      via parent::setParent()
        $order3->setParent($order3);      //via sameObject
    }

    /**
     * @return \AppBundle\Manager\OrderManager|object
     */
    private function getOrderManager()
    {
        return $this->getContainer()->get('order_manager');
    }
}