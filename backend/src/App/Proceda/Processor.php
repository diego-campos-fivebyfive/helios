<?php

namespace App\Proceda;

use App\Sices\Cache\JsonCache;
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

    private $cache;

    const DELIVERING = ['000'];

    const DELIVERED = ['001', '002', '031', '150'];

    const PROCEDA_CACHE = 'OCOREN';

    /**
     * Processor constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;

        $this->cache = JsonCache::create(self::PROCEDA_CACHE);
    }

    public function processEvents($eventGroups)
    {
        foreach ($eventGroups as $invoice => $group) {

            /** @var Order $order */
            $order = $this->manager->findOneBy([
                'invoiceNumber' => $invoice
            ]);

            if ($order) {
                foreach ($group as $event) {
                    $this->changeStatusByEvent($order, $event['event']);
                    // TODO: chamar Timeline
                }
                // TODO: remover do cache
            }
        }
        // TODO: salvar cache
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

    public function mergeEventsAndCache($events)
    {
        foreach ($events as $event) {
            $this->cache->incrementInArrayPosition($event['invoice'], $event);
        }
    }
}
