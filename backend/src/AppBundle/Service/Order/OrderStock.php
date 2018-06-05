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

use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Service\Stock\Component as Stock;

/**
 * Class OrderStock
 * This class processes incoming and outgoing inventory for orders
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderStock
{
    const MODE_DEBIT = -1;
    const MODE_CREDIT = 1;
    const MODE_STATUS = 0;

    /**
     * @var ComponentCollector
     */
    private $collector;

    /**
     * @var Stock
     */
    private $stock;

    /**
     * @var array
     */
    private $transactions;

    /**
     * OrderStock constructor.
     * @param ComponentCollector $collector
     * @param Stock $stock
     */
    function __construct(ComponentCollector $collector, Stock $stock)
    {
        $this->collector = $collector;
        $this->stock = $stock;
        $this->transactions = [];
    }

    /**
     * @param OrderInterface $order
     * @param $mode
     */
    public function process(OrderInterface $order, $mode)
    {
        if ($order->isMaster()) {
            foreach ($order->getChildrens() as $children) {
                $this->mappingTransactions($children, $mode);
            }

            $this->stock->transact($this->transactions);
        }

        $this->transactions = [];
    }

    /**
     * @param OrderInterface $order
     */
    public function credit(OrderInterface $order)
    {
        $this->process($order, self::MODE_CREDIT);
    }

    /**
     * @param OrderInterface $order
     */
    public function debit(OrderInterface $order)
    {
        $this->process($order, self::MODE_DEBIT);
    }

    /**
     * @param OrderInterface $order
     */
    private function mappingTransactions(OrderInterface $order, $mode)
    {
        $reference = $order->isMaster() ? $order->getReference() : $order->getParent()->getReference();
        $deliveryAt = $order->isMaster() ? $order->getDeliveryAt() : $order->getParent()->getDeliveryAt();
        $deliveryInfo = $deliveryAt instanceof \DateTime ? ' - Disp: ' . $deliveryAt->format('d/m/Y') : '';

        $description = sprintf(
            '%s - %s%s',
            $reference,
            (new \DateTime())->format('d/m/Y H:i'),
            $deliveryInfo
        );

        foreach ($order->getElements() as $element) {

            $this->transactions[] = [
                'family' => $element->getFamily(),
                'identity' => $element->getMetadata('id'),
                'amount' => ($element->getQuantity() * $mode),
                'description' => $description
            ];
        }
    }
}
