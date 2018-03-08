<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Misc\Coupon;
use AppBundle\Entity\Misc\CouponInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Manager\CouponManager;
use AppBundle\Manager\ParameterManager;
use Doctrine\Common\Util\ClassUtils;
use AppBundle\Service\Business\RankingGenerator;
use AppBundle\Service\Coupon\Transformer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderCoupon
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $parameter;

    private $maxDiscountPercent;
    /**
     * OrderCoupon constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        /** @var ParameterManager $parameters */
        $parameters = $this->container->get('parameter_manager');

        $this->parameter = $parameters->findOrCreate('platform_settings')->getParameters();

        $this->maxDiscountPercent = $this->parameter['coupon_order_percent'] / 100;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function generateOptions(Order $order)
    {
        $account = $order->getAccount();
        $accountRanking = $account->getRanking();
        $step = $this->parameter['coupon_step_options'];
        if ($accountRanking < $step) {
            return [];
        }

        $maxOrderDiscount = $order->getTotal() * $this->maxDiscountPercent;

        $discountLimit = $maxOrderDiscount < $accountRanking ? $maxOrderDiscount : $accountRanking;

        if ($discountLimit >= $step) {
            if (intval($discountLimit / $step) == 1) {
                return [$step];
            }
            return range($step, intval($discountLimit), $step);
        }
        return [];
    }

    /**
     * @param Order $order
     * @param $coupon
     * @return bool
     */
    public function associateCoupon(Order $order, $coupon)
    {
        /** @var CouponManager $couponManager */
        $couponManager = $this->container->get('coupon_manager');

        if (!$coupon instanceof Coupon) {
            $coupon = $couponManager->findOneBy(['code' => $coupon]);
        }

        if (!$coupon || ($coupon->getAccount() && $order->getAccount() !== $coupon->getAccount())) {
            return false;
        }

        if(!$coupon->getAccount()) {
            $coupon->setAccount($order->getAccount());
        }

        $coupon->setTarget($this->getTarget($order));
        $couponManager->save($coupon);

        $order->setCoupon($coupon);
        $this->container->get('order_manager')->save($order);

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function dissociateCoupon(Order $order)
    {
        if (!$order->getCoupon()){
            return false;
        }

        $coupon = $order->getCoupon();

        $coupon->setTarget(null);
        $order->setCoupon(null);

        try {
            $this->container->get('coupon_manager')->save($coupon);
            $this->container->get('order_manager')->save($order);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param Order $order
     * @param $amount
     * @return mixed|null|object
     */
    public function generateCoupon(Order $order, $amount)
    {
        $ranges = $this->generateOptions($order);

        if (!in_array($amount, $ranges)) {
            return null;
        }

        /** @var Transformer $transformer */
        $transformer = $this->container->get('coupon_transformer');
        $coupon = $transformer->fromAccount($order->getAccount(), $amount);

        if (!$coupon) {
            return null;
        }

        $this->associateCoupon($order, $coupon);
        
        $description = $order->getReference() . " - Resgate de pontos";
        $this->createRanking($order, - $amount, $description);

        return $coupon;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function checkCouponAssociation(Order $order)
    {
        $coupon = $order->getCoupon();
        if (!$coupon || ($coupon->getAppliedBy() != CouponInterface::SOURCE_RANKING)){
            return false;
        }

        $maxOrderDiscount = $order->getTotalWithoutCoupon() * $this->maxDiscountPercent;

        if ($coupon->getAmount() > $maxOrderDiscount){

            $this->dissociateCoupon($order);

            $description = $order->getReference() . " - Crédito de pontos " . $coupon->getCode();
            $this->createRanking($order, $coupon->getAmount(), $description);

            /** @var CouponManager $couponManager */
            $couponManager = $this->container->get('coupon_manager');
            $couponManager->delete($coupon);

            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     * @return string
     */
    private function getTarget(Order $order)
    {
        return sprintf('%s::%s', ClassUtils::getClass($order), $order->getId());
    }

    /**
     * @param Order $order
     * @param $amount
     * @param $description
     */
    private function createRanking(Order $order, $amount, $description)
    {
        /** @var RankingGenerator $rankingGenerator */
        $rankingGenerator = $this->container->get('ranking_generator');
        $rankingGenerator->create($order->getAccount(), $description, $amount);
    }
}
