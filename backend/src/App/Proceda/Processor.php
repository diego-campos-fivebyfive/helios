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

    const DELIVERING = ['000'];

    const DELIVERED = ['001', '002', '031', '150'];

    const PROCEDA_CACHE = 'OCOREN';

    const SEARCH_PREFIX = 'OCOREN';

    /**
     * Processor constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->manager = $this->container->get('order_manager');

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

        foreach ($files as $filename) {
            $content = $this->fileSystem->read($filename);

            $events = Parser::fromContent($content);

            $this->mergeEventsAndCache($events);

            $this->processEvents($this->cache->all());

            $this->fileReader->prefixer($filename, $prefix);
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
                foreach ($group as $event) {
                    $this->changeStatusByEvent($order, $event['event']);
                    // TODO: chamar Timeline
                }

                $this->cache->remove($invoice);
            }
        }

        $this->cache->store();
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
            'host' => getenv('CES_SICES_FTP_HOST'),
            'username' => getenv('CES_SICES_FTP_USER'),
            'password' => getenv('CES_SICES_FTP_PASS'),
            'directory' => '/PROCEDA-SICESSOLAR'
        ]);
    }
}
