<?php

namespace Tests\AppBundle\Service\Order;

use Tests\AppBundle\AppTestCase;

/**
 * Class OrderReferenceTest
 * @group order_reference
 */
class OrderReferenceTest extends AppTestCase
{
    /**
     * Test simple generation
     */
    public function testGenerate()
    {
        $manager = $this->manager('order');
        $reference = $this->getContainer()->get('order_reference');

        $order = $manager->create();

        $manager->save($order);

        $reference->generate($order);

        $this->assertNotNull($order->getId());
        $this->assertNotNull($order->getReference());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWithNotPersisted()
    {
        $order = $this->manager('order')->create();

        $this->getContainer()->get('order_reference')->generate($order);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWithChildOrder()
    {
        $manager = $this->manager('order');
        $order = $manager->create();
        $children = $manager->create();

        $order->addChildren($children);

        $manager->save($order);
        $this->assertNotNull($order->getId());

        $this->getContainer()->get('order_reference')->generate($children);
    }

    public function testOrderReferenceSequence()
    {
        $manager = $this->manager('order');

        $order1 = $manager->create();
        $order2 = $manager->create();
        $order3 = $manager->create();

        $manager->save($order1);
        $manager->save($order2);
        $manager->save($order3);

        $reference = $this->getContainer()->get('order_reference');

        $reference->generate($order1);
        $reference->generate($order2);
        $reference->generate($order3);

        $this->assertLessThan($order2->getReference(), $order1->getReference());
        $this->assertLessThan($order3->getReference(), $order2->getReference());
    }
}
