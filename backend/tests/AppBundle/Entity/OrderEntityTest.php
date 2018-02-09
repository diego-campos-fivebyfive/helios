<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\OrderInterface;
use Tests\App\Generator\GeneratorTest;

/**
 * Class OrderEntityTest
 * @group order_entity
 */
class OrderEntityTest extends GeneratorTest
{
    public function testDiscountConfig()
    {
        $discountConfigFixed = [
            'target' => OrderInterface::DISCOUNT_FIXED,
            'value' => 250
        ];
        $discountConfigPercent = [
            'target' => OrderInterface::DISCOUNT_PERCENT,
            'value' => 10.5
        ];

        $invoices = [
            '2151251612', '1020304050607080', '123123123',
            '10243', '10243', '10241', '46357634857'
        ];

        $invoices2 = ['180207001', '10241'];

        $order = $this->createOrder();

        $order->setInvoices($invoices);
        $order->addInvoice($invoices2);

        $order->removeInvoice(10241);

        self::assertNotNull($order->getInvoices());

        self::assertEquals(1000, $order->getSubTotal());

        $order->setDiscount(200);
        self::assertEquals(200,$order->getDiscount());
        self::assertEquals(800, $order->getTotal());

        $order->setDiscountConfig($discountConfigFixed);
        self::assertEquals(250,$order->getDiscount());
        self::assertEquals(750, $order->getTotal());

        $order->setDiscountConfig($discountConfigPercent);
        self::assertEquals(105,$order->getDiscount());
        self::assertEquals(895, $order->getTotal());

        self::assertEquals(1, $order->getDiscountConfig()['target']);
        self::assertEquals(10.5, $order->getDiscountConfig()['value']);

        $discountConfig = [
            'target' => OrderInterface::DISCOUNT_FIXED,
            'value' => 10
        ];

        $order->setDiscountConfig($discountConfig);

        self::assertEquals(0, $order->getDiscountConfig()['target']);
        self::assertEquals(10, $order->getDiscountConfig()['value']);
    }

    private function createOrder()
    {
        $manager = $this->getContainer()->get('order_manager');

        /** @var OrderInterface $parent */
        $parent = $manager->create();

        for($i= 0; $i < 2; $i++){
            /** @var OrderInterface $order */
            $order = $manager->create();

            $element = new Element();
            $element->setUnitPrice(100);
            $element->setQuantity(5);

            $order->addElement($element);

            $parent->addChildren($order);
        }

        return $parent;
    }
}
