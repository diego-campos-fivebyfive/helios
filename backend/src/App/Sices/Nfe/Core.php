<?php

namespace App\Sices\Nfe;

use App\Sices\Ftp\FileSystemFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Core
{
    /**
     * @var
     */
    private $container;

    /**
     * Core constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function core()
    {
        $parse = $this->container->get('nfe_parser');
        $processor = $this->container->get('nfe_processor');

        $fileSystem = FileSystemFactory::create([
            'host' => getenv('CES_SICES_FTP_HOST'),
            'username' => getenv('CES_SICES_FTP_USER'),
            'password' => getenv('CES_SICES_FTP_PASS'),
            'directory' => '/DANFE'
        ]);

        /** @var FileReader $fileReader */
        $fileReader = $this->container->get('file_Reader');
        $fileReader->init($fileSystem);

        $filesList = array_filter($fileReader->files(), function ($file) {
            $prefix = substr($file, 0, 9);
            return $prefix != "PROCESSED";
        });

        $files = $processor->indexer($filesList);

        foreach ($files as $filename => $extensions) {
            $danfe = $parse::extract($filename);
            $order = $processor->matchReference($danfe);

            if (!$order) {
                continue;
            }

            $processor->setOrderDanfe($order, $danfe, $filename, $extensions);

            $date = (new \DateTime('now'))->format('Ymd');
            $prefix = "PROCESSED-${date}-";

            foreach ($extensions as $extension) {
                $file = "${filename}.${extension}";
                $fileReader->prefixer($file, $prefix);
            }
        }
    }
}
