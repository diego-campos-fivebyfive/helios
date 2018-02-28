<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Misc\Coupon;
use AppBundle\Entity\Order\Order;
use AppBundle\Service\Order\OrderCoupon;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderCouponTest
 * @group order_coupon
 */
class OrderCouponTest extends AppTestCase
{
    public function testAssociate()
    {
        $account = $this->getFixture('account');

        $orderManager = $this->manager('order');
        /** @var Order $order */
        $order = $orderManager->create();
        $order->setAccount($account);
        $orderManager->save($order);

        self::assertNotNull($order);

        $couponManager = $this->manager('coupon');
        /** @var Coupon $coupon */
        $coupon = $couponManager->create();
        $coupon->setName('cupom');
        $coupon->setCode('001AA');
        $coupon->setAccount($account);
        $coupon->setAmount(500);
        $couponManager->save($coupon);

        self::assertNotNull($coupon);

        self::assertNull($order->getCoupon());
        self::assertNull($coupon->getTarget());
        self::assertNull($coupon->getAppliedAt());

        /** @var OrderCoupon $orderCoupon */
        $orderCoupon = $this->service('order_coupon');

        $orderCoupon->associateCoupon($order, $coupon);

        self::assertEquals($coupon, $order->getCoupon());
        self::assertNotNull($coupon->getTarget());
        self::assertNotNull($coupon->getAppliedAt());
    }
}
