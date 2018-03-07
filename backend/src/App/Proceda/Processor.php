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

    /** @var string */
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
     * @var array
     */
    private $timelineList = [];

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

        $this->cache =  dirname(__FILE__) . '/ocoren.json';

        $this->insureCache();
    }

    /**
     * Resolve events
     */
    public function resolve()
    {
        $this->refreshCache();

        $this->processCache();
    }

    /**
     * Create or merge cached events
     */
    public function refreshCache()
    {
        $groups = $this->loadCache();

        $files = $this->fileReader->files(self::SEARCH_PREFIX);

        $contents = $this->loadContents($files);

        $collection = $this->loadCollection($contents);

        $this->mergeGroups($collection, $groups);

        $this->persistCache($groups);

        $this->prefixFiles($files);
    }

    /**
     * Process cached events
     */
    public function processCache()
    {
        $groups = $this->loadCache();

        $this->processGroups($groups);

        $this->persistCache($groups);

        $this->processTimelineList();

        $this->manager->flush();
    }

    /**
     * @param array $files
     * @return array
     */
    private function loadContents(array $files)
    {
        sort($files);
        $contents = [];
        foreach ($files as $file){
            $contents[] = $this->fileReader->read($file);
        }

        return $contents;
    }

    /**
     * @param array $contents
     * @return array
     */
    private function loadCollection(array $contents)
    {
        sort($contents);
        $collection = [];
        foreach ($contents as $content){
            $collection[] = Parser::fromContent($content);
        }

        return $collection;
    }

    /**
     * @param array $collection
     * @param array $events
     */
    private function mergeGroups(array $collection, array &$events)
    {
        foreach ($collection as $data){
            foreach ($data as $event){

                $invoice = $event['invoice'];

                $events[$invoice] = $events[$invoice] ?? [];

                if(!in_array($event, $events[$invoice])) {
                    $events[$invoice][] = $event;
                }
            }
        }
    }

    /**
     * @param array $groups
     */
    private function processGroups(array &$groups = [])
    {
        foreach ($groups as $invoice => $events){

            $this->processGroupEvents($invoice, $events);

            unset($groups[$invoice]);
        }
    }

    /**
     * @param string $invoice
     * @param array $events
     */
    private function processGroupEvents(string $invoice, array $events = [])
    {
        $order = $this->findOrder($invoice);

        if($order instanceof OrderInterface) {

            foreach ($events as $event) {

                $this->changeStatusByEvent($order, $event['event']);

                $this->prepareTimelineEvent($order, $event);
            }
        }
    }

    /**
     * @param Order $order
     * @param array $event
     */
    private function prepareTimelineEvent(Order $order, array $event)
    {
        $status = $order->getStatus();

        $this->timelineList[] = [
            'target' => Resource::getObjectTarget($order),
            'message' => self::MESSAGES[$event['event']],
            'attributes' => [
                'status' => $status,
                'statusLabel' =>  Order::getStatusNames()[$status]
            ]
        ];
    }

    /**
     * @param $invoice
     * @return Order | null
     */
    private function findOrder($invoice)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb
            ->where($qb->expr()->like('o.invoices', $qb->expr()->literal("%${invoice}%")))
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Persist timeline list
     */
    private function processTimelineList()
    {
        $this->timeline->createByArray($this->timelineList);
    }

    /**
     * event resolver
     * @deprecated
     */
    public function legacyResolve()
    {
        /*
        $files = $this->fileReader->files(self::SEARCH_PREFIX);

        $date = (new \DateTime())->format('Ymd');
        $prefix = "PROCESSED-${date}-";

        if (count($files)) {
            sort($files);
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
        */
    }

    /**
     * @param $eventGroups
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @deprecated
     */
    private function processEvents($eventGroups)
    {
        /*
        foreach ($eventGroups as $invoice => $group) {

            $qb = $this->manager->createQueryBuilder();

            $qb
                ->where(
                    $qb->expr()->like('o.invoices', $qb->expr()->literal("%${invoice}%"))
                )
                ->setMaxResults(1);

            $order = $qb->getQuery()->getOneOrNullResult();

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
        */
    }

    /**
     * @param $group
     * @deprecated
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
     * @deprecated
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
     * @param int $event
     */
    private function changeStatusByEvent(Order $order, $event)
    {
        $status = $order->getStatus();

        if ($status != OrderInterface::STATUS_DELIVERED
            && in_array($event, self::DELIVERING)) {
            $order->setStatus(OrderInterface::STATUS_DELIVERING);
        } elseif ($status != OrderInterface::STATUS_DELIVERED
            && in_array($event, self::DELIVERED)) {
            $order->setStatus(OrderInterface::STATUS_DELIVERED);
        }

        $this->manager->save($order, false);
    }

    /**
     * @param array $files
     */
    private function prefixFiles(array $files)
    {
        $date = (new \DateTime())->format('Ymd');
        $prefix = "PROCESSED-${date}-";

        foreach ($files as $filename) {
            $this->fileReader->prefixer($filename, $prefix);
        }
    }

    /**
     * @param $events
     * @deprecated
     */
    private function mergeEventsAndCache($events)
    {
        /*foreach ($events as $event) {
            $this->cache->incrementInArrayPosition($event['invoice'], $event);
        }*/
    }

    /**
     * Insure cache exists
     */
    private function insureCache()
    {
        if(!file_exists($this->cache)){
            $this->persistCache([]);
        }
    }

    /**
     * @return array
     */
    private function loadCache()
    {
        return json_decode(file_get_contents($this->cache), true) ?? [];
    }

    /**
     * @param array $data
     */
    private function persistCache(array $data)
    {
        $handle = fopen($this->cache, 'w+');
        fwrite($handle, json_encode($data));
        fclose($handle);
    }

    /**
     * @return \Gaufrette\Filesystem
     */
    private function connect()
    {
        return FileSystemFactory::create([
            'host' => $this->container->getParameter('ftp_host'),
            'port' => $this->container->getParameter('ftp_port'),
            'username' => $this->container->getParameter('ftp_user'),
            'password' => $this->container->getParameter('ftp_password'),
            'directory' => '/ftp/PROCEDA-SICESSOLAR'
        ]);
    }
}
