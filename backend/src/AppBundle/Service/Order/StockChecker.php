<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StockChecker
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * StockChecker constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Order $order
     * @return array
     */
    public function groupComponents(Order $order)
    {
        $groups = [];

        if($order->isMaster()) {

            /** @var Order $suborder */
            foreach ($order->getChildrens() as $suborder) {

                foreach ($suborder->getElements() as $element) {

                    $family = $element->getFamily();
                    $code = $element->getCode();

                    if (!array_key_exists($family, $groups)) {
                        $groups[$family] = [];
                    }

                    if (!array_key_exists($code, $groups[$family])) {
                        $groups[$family][$code] = [
                            'description' => $element->getDescription(),
                            'quantity' => 0
                        ];
                    }

                    $groups[$family][$code]['quantity'] += $element->getQuantity();
                }
            }
        }

        return $groups;
    }
}
