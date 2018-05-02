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
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Processor
{
    /** @var OrderManager */
    private $manager;

    /** @var string */
    private $cache = 'ocoren.json';

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

    const PROCESSED_DIR = 'PROCESSED/';

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
     * @var array
     */
    private $result = [
        'loaded_files' => 0,
        'loaded_events' => 0,
        'cached_events' => 0
    ];

    /**
     * Processor constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        set_time_limit(180);

        ini_set('memory_limit', '1024M');

        $this->container = $container;

        $this->manager = $this->container->get('order_manager');

        $this->timeline = $this->container->get('timeline_resource');

        $this->fileSystem = $this->connect();

        $this->fileReader = $this->container->get('file_reader');

        $this->fileReader->init($this->fileSystem);

        $this->insureCache();
    }

    /**
     * @return array
     */
    public function resolve()
    {
        $this->refreshCache();

        $this->processCache();

        return $this->result;
    }

    /**
     * Create or merge cached events
     */
    public function refreshCache()
    {
        $groups = $this->loadCache();

        $files = $this->loadAndFilterFiles();

        $this->result['loaded_files'] = count($files);

        $contents = $this->loadContents($files);

        $collection = $this->loadCollection($contents);

        $this->mergeGroups($collection, $groups);

        $this->persistCache($groups);

        $this->moveProcessedFiles($files);
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
     * @return array
     */
    private function loadAndFilterFiles()
    {
        $current = $this->fileReader->files(self::SEARCH_PREFIX);
        $processed = $this->fileReader->files(self::PROCESSED_DIR);

        return array_diff($current, array_map(function($path){
            return explode('/', $path)[1];
        }, $processed));
    }

    /**
     * @param array $files
     * @return array
     */
    private function loadContents(array $files)
    {
        sort($files);
        return array_map(function($file){
            return $this->fileReader->read($file);
        }, $files);
    }

    /**
     * @param array $contents
     * @return array
     */
    private function loadCollection(array $contents)
    {
        return array_map(function($content){
            return Parser::fromContent($content);
        }, $contents);
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
        $this->result['loaded_events'] = array_sum(array_map('count', $groups));

        $orders = $this->findOrders(array_keys($groups));

        foreach ($orders as $invoice => $order){
            if(array_key_exists($invoice, $groups)) {

                $this->processGroupEvents($orders[$invoice], (array) $groups[$invoice]);

                unset($groups[$invoice]);
            }
        }

        $this->result['cached_events'] = array_sum(array_map('count', $groups));
    }

    /**
     * @param Order $order
     * @param array $events
     */
    private function processGroupEvents(Order $order, array $events = [])
    {
        foreach ($events as $event) {
            $this->changeStatusByEvent($order, $event);
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
     * @deprecated Consulta otimizada em findOrders
     */
    private function findOrder($invoice)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb
            ->select('o.id')
            ->where($qb->expr()->like('o.invoices', $qb->expr()->literal("%${invoice}%")))
            ->setMaxResults(1);

        $ids = $qb->getQuery()->getOneOrNullResult();

        return $ids ? array_map('current', $ids) : null ;
    }

    /**
     * Persist timeline list
     */
    private function processTimelineList()
    {
        $this->timeline->createByArray($this->timelineList);
    }

    /**
     * @param Order $order
     * @param array $event
     */
    private function changeStatusByEvent(Order $order, $event)
    {
        if($order->getStatus() != Order::STATUS_DELIVERED
            && in_array($event['code'], array_merge(self::DELIVERING, self::DELIVERED))){

            $status = in_array($event['code'], self::DELIVERED)
                ? Order::STATUS_DELIVERED
                : Order::STATUS_DELIVERING ;

            $order->setStatus($status);

            $this->prepareTimelineEvent($order, $event);
        }

        $this->manager->save($order, false);
    }

    /**
     * @param array $files
     */
    private function moveProcessedFiles(array $files)
    {
        foreach ($files as $filename) {
            $this->fileReader->prefixer($filename, 'PROCESSED/');
        }
    }

    /**
     * Insure cache exists
     */
    private function insureCache()
    {
        $cache = current($this->fileReader->files($this->cache));

        if(!$cache){
            $this->persistCache([]);
        }
    }

    /**
     * @return array
     */
    private function loadCache()
    {
        $cache = current($this->fileReader->files($this->cache));

        return $cache ? (array) json_decode($this->fileReader->read($cache), true) : [];
    }

    /**
     * @param array $data
     */
    private function persistCache(array $data)
    {
        $this->fileReader->write($this->cache, json_encode($data),  true);
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
            'directory' => '/PROCEDA-SICESSOLAR'
        ]);
    }

    /**
     * @param array $invoices
     * @return array
     */
    private function findOrders(array $invoices)
    {
        if(empty($invoices)){
            return [];
        }

        $regex = implode('|', $invoices);

        $SQL = "SELECT id FROM app_order WHERE invoices REGEXP '{$regex}'";

        $stmt = $this->manager->getConnection()->prepare($SQL);
        $stmt->execute();

        $ids = array_map('current', $stmt->fetchAll());

        if(empty($ids)){
            return [];
        }

        $qb = $this->manager->createQueryBuilder();

        $qb->where($qb->expr()->in('o.id', $ids));

        $result = $qb->getQuery()->getResult();
        $orders = [];
        /** @var Order $order */
        foreach ($result as $order){
            foreach ($order->getInvoices() as $invoice){
                $orders[$invoice] = $order;
            }
        }

        return $orders;
    }
}
