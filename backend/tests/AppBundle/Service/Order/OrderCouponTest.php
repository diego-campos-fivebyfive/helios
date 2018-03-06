<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Misc\Coupon;
use AppBundle\Entity\Misc\CouponInterface;
use AppBundle\Entity\Order\Element;
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

      //  self::assertNotNull($order);

        $couponManager = $this->manager('coupon');
        /** @var Coupon $coupon */
        $coupon = $couponManager->create();
        $coupon->setName('cupom');
        $coupon->setCode('001AA');
        $coupon->setAccount($account);
        $coupon->setAmount(500);
        $coupon->setAppliedBy(CouponInterface::SOURCE_CODE);
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

    public function testCheckAssociation()
    {
        /** @var AccountInterface $account */
        $account = $this->getFixture('account');
        $account->setRanking(10000);

        $this->manager('account')->save($account);

        $orderManager = $this->manager('order');
        /** @var Order $order */
        $order = $orderManager->create();
        $order->setAccount($account);

        $element = new Element();

        $element->setQuantity(100);
        $element->setUnitPrice(100);

        $order->addElement($element);

        self::assertNotNull($order);

        $couponManager = $this->manager('coupon');
        /** @var Coupon $coupon */
        $coupon = $couponManager->create();
        $coupon->setName('cupom');
        $coupon->setCode('001AA');
        $coupon->setAccount($account);
        $coupon->setAmount(5000);
        $coupon->setAppliedBy(CouponInterface::SOURCE_RANKING);
        $coupon->setTarget(null);
        $couponManager->save($coupon);

        $order->setCoupon($coupon);

        $orderManager->save($order);

        self::assertEquals(10000, $order->getAccount()->getRanking());
        self::assertEquals(10000, $order->getTotalWithoutCoupon());
        self::assertEquals(5000, $order->getTotal());

        /** @var OrderCoupon $orderCoupon */
        $orderCoupon = $this->service('order_coupon');

        $check = $orderCoupon->checkCouponAssociation($order);

        self::assertEquals(true, $check);
        self::assertNotNull($order->getCoupon());

        /** @var Element $element */
        foreach ($order->getElements() as $element) {
            $element->setUnitPrice(90);
        }

        self::assertEquals(9000, $order->getTotalWithoutCoupon());

        $orderManager->save($order);

        $check = $orderCoupon->checkCouponAssociation($order);

        self::assertEquals(false, $check);
        self::assertNull($order->getCoupon());
    }
}
