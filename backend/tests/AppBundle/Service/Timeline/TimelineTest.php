<?php

namespace Tests\AppBundle\Service\Timeline;

use AppBundle\Service\Timeline\Resource;
use Tests\AppBundle\AppTestCase;

/**
 * Class TimelineTest
 * @group timeline_service
 */
class TimelineTest extends AppTestCase
{
    public function testCreate()
    {
        $order = $this->createOrder();

        self::assertNotNull($order);

        $timeline = $this->service('timeline');

        $target = Resource::getObjectTarget($order);

        $attributes = ['status'=>2];

        $newTimeline = $timeline->create($target, 'msg', $attributes);

        self::assertEquals($target, $newTimeline->getTarget());
        self::assertEquals('msg', $newTimeline->getMessage());
        self::assertEquals($attributes, $newTimeline->getAttributes());


        $date = new \DateTime('2015-08-21');

        $timelines = [
            [
                'target' => 'AppBundle\Entity\Order\Order::1',
                'message' => 'msg 1',
                'attributes' => ['a'=>1]
            ],
            [
                'target' => 'AppBundle\Entity\Order\Order::1',
                'message' => 'msg 2',
                'attributes' => ['b'=>2],
                'createdAt' => $date],
            [
                'target' => 'AppBundle\Entity\Order\Order::1',
                'message' => 'msg 3',
                'attributes' => null
            ]
        ];

        $timeline = $this->service('timeline');

        $timeline->createByArray($timelines);

        self::assertEquals($timeline->loadByTarget('AppBundle\Entity\Order\Order::1'), $timeline->loadByObject($order));

        self::assertEquals(4, count($timeline->loadByObject($order)));

        self::assertEquals(2, $timeline->loadByObject($order)[3]->getAttributes()['status']);
    }

    private function createOrder()
    {
        $manager = $this->manager('order');

        /** @var \AppBundle\Entity\Order\Order $order */
        $order = $manager->create();
        $manager->save($order);
        return $order;
    }
}
