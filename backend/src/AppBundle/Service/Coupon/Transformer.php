<?php

namespace AppBundle\Service\Coupon;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Misc\Coupon;
use AppBundle\Manager\CouponManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Transformer
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CouponManager
     */
    private $manager;

    /**
     * Transformer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $this->container->get('coupon_manager');
    }

    /**
     * @param AccountInterface $account
     * @param $amount
     * @return mixed|object
     */
    public function fromAccount(AccountInterface $account, $amount)
    {
        if ($account->getRanking() >= $amount) {

            $coupon = $this->manager->create();
            $name = "Cupom de desconto";

            $coupon
                ->setAmount($amount)
                ->setName($name)
                ->setAccount($account);
            $this->manager->save($coupon);

            $this->addCodeCoupon($coupon);

            return $coupon;
        }
    }

    /**
     * @param $code
     * @return null|object
     */
    public function getCoupon($code)
    {
        return $this->manager->findOneBy(["code" => $code]);
    }

    /**
     * @param Coupon $coupon
     * @return string
     */
    public function generateCode(Coupon $coupon)
    {
        return substr(strtoupper(md5(uniqid()) . $coupon->getId()), -6);
    }

    /**
     * @param Coupon $coupon
     */
    private function addCodeCoupon(Coupon $coupon)
    {
        $code = $this->generateCode($coupon);

        $coupon->setCode($code);

        $this->manager->save($coupon);
    }

}
