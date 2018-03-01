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
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCoupon($code)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->where("c.code = :code")
            ->setParameter(
                "code" , $code
            )
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Coupon $coupon
     */
    private function addCodeCoupon(Coupon $coupon)
    {
        $code = substr(strtoupper(md5(uniqid()) . $coupon->getId()), -6);

        $coupon->setCode($code);

        $this->manager->save($coupon);
    }

}
