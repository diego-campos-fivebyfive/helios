<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Misc\CouponInterface;
use AppBundle\Manager\AccountManager;
use AppBundle\Manager\CouponManager;
use Tests\AppBundle\AppTestCase;

/**
 * @group coupon
 */
class CouponManagerTest extends AppTestCase
{
    public function testDefault()
    {
        /** @var AccountInterface $account */
        $account = $this->createAccount();
        self::assertNotNull($account);

        /** @var CouponInterface $coupon */
        $coupon = $this->createCoupon($account);

        self::assertNotNull($coupon);

        self::assertEquals(2.2, $coupon->getAmount());
        self::assertEquals('terget::1', $coupon->getTarget());
        self::assertEquals($account, $coupon->getAccount());

        /** @var CouponManager $manager */
        $manager = $this->getContainer()->get('coupon_manager');

        $couponTarget = $manager->findByTarget('terget::1');
        $couponAccount = $manager->findByAccount($account);

        self::assertEquals($couponTarget[0]->getId(), $couponAccount[0]->getId());
        self::assertEquals($couponTarget[0]->getTarget(), $couponAccount[0]->getTarget());
        self::assertEquals($couponTarget[0]->getAmount(), $couponAccount[0]->getAmount());
        self::assertEquals($couponTarget[0]->getAppliedAt(), $couponAccount[0]->getAppliedAt());
        self::assertEquals($couponTarget[0]->getAccount(), $couponAccount[0]->getAccount());

    }

    private function createCoupon($account)
    {
        $manager = $this->getContainer()->get('coupon_manager');

        /** @var CouponInterface $coupon */
        $coupon = $manager->create();

        $coupon
            ->setName('Teste um')
            ->setAmount(2.2)
            ->setTarget('terget::1')
            ->setCode('123')
            ->setAccount($account)
        ;

        $manager->save($coupon);

        return $coupon;
    }

    private function createAccount()
    {
        /** @var AccountManager $accountManager */
        $accountManager = $this->getContainer()->get('customer_manager');

        $account = $accountManager->create();

        $account->setContext(Customer::CONTEXT_ACCOUNT);

        $accountManager->save($account);

        return $account;
    }

}
