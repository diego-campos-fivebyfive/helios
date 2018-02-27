<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Proceda;

use App\Sices\Cache\JsonCache;
use App\Sices\Ftp\FileReader;
use App\Sices\Ftp\FileSystemFactory;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Manager\OrderManager;
use AppBundle\Service\Timeline\Resource;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Processor
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Processor
{
    /** @var OrderManager */
    private $manager;

    /** @var JsonCache */
    private $cache;

    /** @var ContainerInterface */
    private $container;

    /** @var \Gaufrette\Filesystem */
    private $fileSystem;

    /** @var FileReader */
    private $fileReader;

    private $timeline;

    const DELIVERING = ['000'];

    const DELIVERED = ['001', '002', '031', '105'];

    const PROCEDA_CACHE = 'OCOREN';

    const SEARCH_PREFIX = 'OCOREN';

    const MESSAGES = [
        '000' => 'Processo de Transporte já Iniciado',
        '001' => 'Entrega Realizada Normalmente',
        '002' => 'Entrega Fora da Data Programada',
        '031' => 'Entrega com Indenização Efetuada',
        '105' => 'Entrega efetuada no cliente pela Transportadora de Redespacho'
    ];

    /**
     * Processor constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->manager = $this->container->get('order_manager');

        $this->timeline = $this->container->get('timeline_resource');

        $this->fileSystem = $this->connect();

        $this->fileReader = $this->container->get('file_reader');

        $this->fileReader->init($this->fileSystem);

        $this->cache = JsonCache::create(self::PROCEDA_CACHE);
    }

    /**
     * event resolver
     */
    public function resolve()
    {
        $files = $this->fileReader->files(self::SEARCH_PREFIX);

        $date = (new \DateTime())->format('Ymd');
        $prefix = "PROCESSED-${date}-";

        if (count($files)) {
            foreach ($files as $filename) {
                $content = $this->fileSystem->read($filename);

                $events = Parser::fromContent($content);

                $this->mergeEventsAndCache($events);

                $this->processEvents($this->cache->all());

                $this->fileReader->prefixer($filename, $prefix);
            }
        } else {
            $this->processEvents($this->cache->all());
        }
    }

    /**
     * @param $eventGroups
     */
    private function processEvents($eventGroups)
    {
        foreach ($eventGroups as $invoice => $group) {
            /** @var Order $order */
            $order = $this->manager->findOneBy([
                'invoiceNumber' => $invoice
            ]);

            if ($order) {
                $timelineList = [];

                $target = Resource::getObjectTarget($order);

                $this->sortEventsByDate($group);

                foreach ($group as $event) {
                    $this->changeStatusByEvent($order, $event['event']);

                    $message = self::MESSAGES[$event['event']];
                    $status = $order->getStatus();

                    $timelineList[] = [
                        'target' => $target,
                        'message' => $message,
                        'attributes' => [
                            'status' => $status,
                            'statusLabel' => Order::getStatusNames()[$status]
                        ]
                    ];
                }

                $this->timeline->createByArray($timelineList);

                $this->cache->remove($invoice);
            }
        }

        $this->cache->store();
    }

    /**
     * @param $group
     */
    private function sortEventsByDate(&$group)
    {
        usort($group, function ($event1, $event2) {
            $date1 = $this->formatDate($event1['date']);
            $date2 = $this->formatDate($event2['date']);

            $dateTime1 = "${date1}${event1['time']}";
            $dateTime2 = "${date2}${event2['time']}";

            if ($dateTime1 == $dateTime2) {
                return 0;
            }

            return ($dateTime1 < $dateTime2) ? -1 : 1;
        });
    }

    /**
     * @param $date
     * @return string
     */
    private function formatDate($date)
    {
        $day = substr($date,0, 2);
        $month = substr($date,2, 2);
        $year = substr($date,4, 4);

        return "${year}${month}${day}";
    }

    /**
     * @param Order $order
     * @param $event
     */
    private function changeStatusByEvent($order, $event)
    {
        $status = $order->getStatus();

        if ($status != OrderInterface::STATUS_DELIVERED
            && in_array($event, self::DELIVERING)) {
            $order->setStatus(OrderInterface::STATUS_DELIVERING);
            $this->manager->save($order);
        } elseif ($status != OrderInterface::STATUS_DELIVERED
            && in_array($event, self::DELIVERED)) {
            $order->setStatus(OrderInterface::STATUS_DELIVERED);
            $this->manager->save($order);
        }
    }

    /**
     * @param $events
     */
    private function mergeEventsAndCache($events)
    {
        foreach ($events as $event) {
            $this->cache->incrementInArrayPosition($event['invoice'], $event);
        }
    }

    /**
     * @return \Gaufrette\Filesystem
     */
    private function connect()
    {
        return FileSystemFactory::create([
            'host' => $this->container->getParameter('ftp_host'),
            'username' => $this->container->getParameter('ftp_user'),
            'password' => $this->container->getParameter('ftp_password'),
            'directory' => '/PROCEDA-SICESSOLAR'
        ]);
    }
}
