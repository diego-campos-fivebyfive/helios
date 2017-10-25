<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Order;

use AppBundle\Manager\OrderManager;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Service\Util\PlatformCounter;

/**
 * Class OrderReference
 * This class generate orders reference with defined format
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderReference
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * @var PlatformCounter
     */
    private $counter;

    /**
     * @var string
     */
    private $counterKey = 'orders';

    /**
     * OrderReference constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager, PlatformCounter $counter)
    {
        $this->manager = $manager;
        $this->counter = $counter;
    }

    /**
     * @param OrderInterface $order
     * @return null|string
     */
    public function generate(OrderInterface $order)
    {
        $this->checkOrder($order);

        if(null == $reference = $order->getReference()) {

            $date = new \DateTime();

            $reference = sprintf('%02d%02d%02d%06d',
                $date->format('y'),
                $date->format('n'),
                $date->format('j'),
                $this->counter->next($this->counterKey)
            );

            $order->setReference($reference);

            $this->manager->save($order);
        }

        return $reference;
    }

    /**
     * @param OrderInterface $order
     */
    private function checkOrder(OrderInterface $order)
    {
        if(!$order->getId())
            $this->exception('The order is not persisted');

        if($order->isChildren())
            $this->exception('Suborders can not receive reference');
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
