<?php

namespace App\Sices\Nfe;

use App\Sices\Ftp\FileReader;
use App\Sices\Ftp\FileSystemFactory;
use AppBundle\Entity\Order\Order;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Core
{
    /**
     * @var
     */
    private $container;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $result = [
        'loaded_files' => 0,
        'processed_files' => 0
    ];

    /**
     * Core constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->path = "{$container->get('kernel')->getRootDir()}/../../.uploads/fiscal/danfe/";
    }

    public function core()
    {
        /** @var Processor $processor */
        $processor = $this->container->get('nfe_processor');

        $fileSystem = FileSystemFactory::create([
            'host' => $this->container->getParameter('ftp_host'),
            'port' => $this->container->getParameter('ftp_port'),
            'username' => $this->container->getParameter('ftp_user'),
            'password' => $this->container->getParameter('ftp_password'),
            'directory' => '/DANFE'
        ]);

        /** @var FileReader $fileReader */
        $fileReader = $this->container->get('file_Reader');
        $fileReader->init($fileSystem);

        $filesList = array_filter($fileReader->files(), function ($file) {
            $prefix = substr($file, 0, 9);
            return $prefix != "PROCESSED";
        });

        $this->result['loaded_files'] = count($filesList);

        $fileReader->downloadList($filesList, $this->path);

        $processor->pushS3($filesList, $this->path);

        $files = $processor->indexer($filesList);

        $ordersReferences = [];
        $ordersData = [];
        foreach ($files as $filename => $extensions) {
            $danfe = Parser::extract($filename);
            $ordersReferences[] = $danfe['reference'];
            $ordersData[$danfe['reference']]['danfe'] = $danfe;
            $ordersData[$danfe['reference']]['filename'] = $filename;
            $ordersData[$danfe['reference']]['extensions'] = $extensions;
        }

        $orders = $processor->matchReferences($ordersReferences);

        /** @var Order $order */
        foreach ($orders as $order) {
            $danfe = $ordersData[$order->getReference()]['danfe'];
            $filename = $ordersData[$order->getReference()]['filename'];
            $extensions = $ordersData[$order->getReference()]['extensions'];

            $processor->setOrderDanfe($order, $danfe, $filename, $extensions);

            $date = (new \DateTime('now'))->format('Ymd');
            $prefix = "PROCESSED-${date}-";

            foreach ($extensions as $extension) {
                $file = "${filename}.${extension}";
                $fileReader->prefixer($file, $prefix);
                $this->result['processed_files']++;
            }
        }

        return $this->result;
    }
}
