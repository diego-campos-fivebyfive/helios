<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderRanking
{
    /**
     * @var Customer
     */
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @var array
     */
    private static $mapping = [
        Memorial::LEVEL_TITANIUM => 20,
        Memorial::LEVEL_BLACK => 10,
        Memorial::LEVEL_PLATINUM => 7,
        Memorial::LEVEL_PREMIUM => 5,
        Memorial::LEVEL_PARTNER => 3
    ];

    /**
     * @return array
     */
    public static function getMapping()
    {
        return self::$mapping;
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function generate(Order $order)
    {
        if ($order->isMaster() && $order->getLevel()) {

            $deliveryInfo = $order->getDeliveryAt() instanceof \DateTime ? ' - Disp: ' . $order->getDeliveryAt()->format('d/m/Y') : '';

            $description = sprintf(
                '%s - %s%s',
                $order->getReference(),
                (new \DateTime())->format('d/m/Y H:i'),
                $deliveryInfo
            );

            $amount = ceil($order->getPower() * self::$mapping[$order->getLevel()]);

            $account = $order->getAccount();

            $target = $account->isChildAccount() ? $account->getParentAccount() : $account;

            return $this->rankingGenerator()->create($target, $description, $amount);
        }

        return;
    }

    /**
     * @return \AppBundle\Service\Business\RankingGenerator
     */
    private function rankingGenerator()
    {
        return $this->container->get('ranking_generator');
    }
}
