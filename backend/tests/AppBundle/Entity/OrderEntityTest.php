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
    public function testTags() {
        /** @var OrderInterface $order */
        $order = $this->createOrder();

        $tag = [
            'name' => "Nome da tag",
            'initials' => "TG",
            'tag_color' => "#000",
            'text_color' => "#FFF"
        ];

        $role = "ROLE_PLATFORM_COMMERCIAL";

        $role2 = "ROLE_PLATFORM_FINANCIAL";

        $roleEmpty = "ROLE_EMPTY";

        self::assertEquals([], $order->getTags());

        $order->addTag($tag, $role);

        $order->addTag($tag, $role2);

        $order->addTag($tag, $role2);

        self::assertEquals(1, count($order->getTags($role)));

        self::assertEquals(2, count($order->getTags()));

        self::assertEquals(2, count($order->getTags()[$role2]));

        $t = $order->getTags($role);

        $key = array_keys($t)[0];

        self::assertEquals($key, $t[$key]['id']);

        self::assertEquals(1, count($t));

        $order->removeTag($role, $key);

        $t = $order->getTags($role);

        self::assertEquals(0, count($t));

        self::assertEquals([], $order->getTags($roleEmpty));
    }

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
