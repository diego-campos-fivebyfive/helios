<?php

namespace App\Proceda;

use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Manager\OrderManager;

/**
 * Class Processor
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Processor
{
    /**
     * @var OrderManager
     */
    private $manager;

    const DELIVERING = ['000'];

    const DELIVERED = ['001', '002', '031', '150'];

    /**
     * Processor constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;
    }

    public function processEvents($eventGroups)
    {
        $cache = [];

        foreach ($eventGroups as $invoice => $group) {

            /** @var Order $order */
            $order = $this->manager->findOneBy([
                'invoiceNumber' => $invoice
            ]);

            if ($order) {
                foreach ($group as $event) {
                    $this->changeStatusByEvent($order, $event['event']);
                }
            } else {
                // TODO: ajustar metodo de cache para adicionar todos de uma vez e salvar no final ou ir adicionando e salvar
                $cache[$invoice] = $group;
            }

            // TODO: chamar Timeline
        }
    }

    /**
     * @param Order $order
     * @param $event
     */
    private function changeStatusByEvent($order, $event)
    {
        $status = $order->getStatus();

        if (in_array($event, self::DELIVERING)) {
            $status = OrderInterface::STATUS_DELIVERING;
        } elseif (in_array($event, self::DELIVERED)) {
            $status = OrderInterface::STATUS_DELIVERED;
        }

        $order->setStatus($status);

        $this->manager->save($order);
    }
}
