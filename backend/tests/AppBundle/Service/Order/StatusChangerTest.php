<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use Tests\AppBundle\AppTestCase;

/**
 * Class StatusChangerTest
 * @group order_status_changer
 */
class StatusChangerTest extends AppTestCase
{
    public function testDefaultStatusChange()
    {
        $this->createParameters();

        $manager = $this->manager('order');

        $order = $manager->create();

        $manager->save($order);

        $this->assertTrue($order->isBuilding());

        $changer = $this->service('order_status_changer');

        $changer->change($order, OrderInterface::STATUS_PENDING);

        $this->assertTrue($order->isPending());
        $this->assertEquals(Order::STATUS_BUILDING, $order->getPreviousStatus());
    }

    /**
     * Create a default usage parameters
     */
    private function createParameters()
    {
        $manager = $this->manager('parameter');

        /** @var \AppBundle\Entity\Parameter $parameter */
        $parameter = $manager->findOrCreate('platform_settings');

        $parameter->setParameters([
            'order_expiration_days' => [
                Order::STATUS_PENDING => 5
            ]
        ]);

        $manager->save($parameter);
    }
}
