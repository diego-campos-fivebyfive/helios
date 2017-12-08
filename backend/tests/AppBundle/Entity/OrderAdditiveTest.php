<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Misc\AdditiveInterface;
use AppBundle\Entity\Order\OrderAdditiveInterface;
use AppBundle\Entity\Pricing\Memorial;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderManagerTest
 * @group order_additive
 */
class OrderAdditiveTest extends AppTestCase
{
    public function testRelation()
    {
        $orderManager = $this->getContainer()->get('order_manager');

        $order = $orderManager->create();
        $orderManager->save($order);

        self::assertNotNull($order);

        $additiveManager = $this->getContainer()->get('additive_manager');

        $levels = ['premium', 'partner', 'black'];
        /** @var AdditiveInterface $additive */
        $additive = $additiveManager->create();
        $additive->setName('add');
        $additive->setType(4);
        $additive->setTarget(3);
        $additive->setValue(3.5);
        $additive->setRequiredLevels($levels);
        $additive->setAvailableLevels($levels);

        $additiveManager->save($additive);

        self::assertNotNull($additive);

        $orderAdditiveManager = $this->getContainer()->get('order_additive_manager');

        /** @var OrderAdditiveInterface $orderAdditive */
        $orderAdditive = $orderAdditiveManager->create();

        $orderAdditive->setAdditive($additive);
        $orderAdditive->setOrder($order);

        $orderAdditiveManager->save($orderAdditive);

        self::assertNotNull($orderAdditive);

        self::assertEquals(1,$additive->isRequiredByLevels(Memorial::LEVEL_PARTNER));
        self::assertEquals(1, $additive->isAvailableByLevels(Memorial::LEVEL_PREMIUM));
        self::assertEquals(1,$orderAdditive->getOrder()->getId());
        self::assertEquals(1,$orderAdditive->getAdditive()->getId());
    }
}
