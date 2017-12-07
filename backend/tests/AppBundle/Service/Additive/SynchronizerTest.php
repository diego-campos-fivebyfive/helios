<?php

namespace Tests\AppBundle\Service\Additive;

use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderAdditive;
use Tests\AppBundle\AppTestCase;

/**
 * Class SynchronizerTest
 * @group additive_sync
 */
class SynchronizerTest extends AppTestCase
{
    public function testDefaultSynchronization()
    {
        $manager = $this->manager('order');

        /** @var Order $order */
        $order = $manager->create();

        $additives = $this->createAdditives();

        $this->associateAdditives($order, $additives);

        $synchronizer = $this->service('additive_synchronizer');

        $manager->save($order);

        $synchronizer->synchronize($order);

        $this->assertCount(10, $order->getOrderAdditives()->toArray());

        // Disable additives
        $additiveManager = $this->manager('additive');
        for($i=0; $i < 3; $i++){
            /** @var Additive $additive */
            $additive = $additives[$i];

            $additive->setEnabled(false);

            $additiveManager->save($additive);
        }

        $synchronizer->synchronize($order);

        $this->assertCount(7, $order->getOrderAdditives()->toArray());
    }

    /**
     * @return array
     */
    private function createAdditives()
    {
        $manager = $this->manager('additive');

        $additives = [];
        for($i=0; $i < 10; $i++){

            /** @var Additive $additive */
            $additive = $manager->create();

            $additive
                ->setName('Additive ' . ($i+1))
                ->setDescription('This is a test additive ' . ($i+1))
                ->setEnabled(true)
                ->setTarget(Additive::TARGET_FIXED)
                ->setType(Additive::TYPE_INSURANCE)
                ->setValue(100);

            $manager->save($additive);

            $additives[] = $additive;
        }

        return $additives;
    }

    /**
     * @param Order $order
     * @param array $additives
     */
    private function associateAdditives(Order $order, array $additives)
    {
        foreach ($additives as $additive){

            $orderAdditive = new OrderAdditive();

            $orderAdditive
                ->setAdditive($additive)
                ->setOrder($order)
            ;
        }
    }
}
