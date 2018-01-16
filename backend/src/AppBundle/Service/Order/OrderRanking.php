<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Pricing\Memorial;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderRanking
{
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
    private $mapping = [
        Memorial::LEVEL_BLACK => 3,
        Memorial::LEVEL_PLATINUM => 2,
        Memorial::LEVEL_PREMIUM => 1,
        Memorial::LEVEL_PARTNER => 0.5
    ];

    /**
     * @param $order
     * @return array
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

            $amount = ceil($order->getPower() * $this->mapping[$order->getLevel()]);
            $target = $order->getAccount();

            return $this->rankingGenerator()->create($target, $description, $amount);
        }

        return;

    }

    /**
     * @return \AppBundle\Service\Business\RankingGenerator
     */
    private function rankingGenerator()
    {
        return $this->container->get('ranking_generator');;
    }
}
