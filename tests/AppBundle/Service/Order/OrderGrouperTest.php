<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderGrouperTest
 * @group order_grouper
 */
class OrderGrouperTest extends AppTestCase
{
    public function testDefaultGrouper()
    {
        $childrens = $this->createOrders(5);
        $total = 0;
        foreach ($childrens as $children){
            $total += $children->getTotal();
        }

        $grouper = $this->getContainer()->get('order_transformer');

        $order = $grouper->transformFromChildrens($childrens);

        $this->assertEquals(5, $order->getChildrens()->count());
        $this->assertEquals($total, $order->getTotal());
        $this->assertNotNull($order->getId());
        $this->assertNotNull($order->getAccount());
    }

    private function createOrders($number)
    {
        $manager = $this->manager('order');
        $account = $this->getFixture('account');

        $orders = [];
        for($i= 0; $i < $number; $i++){
            $order = $manager->create();

            $element = new Element();
            $element
                ->setCode('ABCD')
                ->setDescription('The Element')
                ->setUnitPrice(1000)
                ->setQuantity($i+1)
            ;

            $order
                ->setAccount($account)
                ->addElement($element);

            $orders[] = $order;
        }

        return $orders;
    }
}