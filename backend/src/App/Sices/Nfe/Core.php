<?php

namespace App\Sices\Nfe;

use App\Sices\Ftp\FileReader;
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
        $parse = new Parser();
        $processor = $this->container->get('nfe_processor');

        $fileSystem = FileSystemFactory::create([
            'host' => getenv('CES_SICES_FTP_HOST'),
            'username' => getenv('CES_SICES_FTP_USER'),
            'password' => getenv('CES_SICES_FTP_PASS'),
            'directory' => '/DANFE'
        ]);

        $fileReader = new FileReader($fileSystem, $this->container);

        $files = $processor->indexer($fileReader->files());

        foreach ($files as $filename => $extensions) {
            $danfe = $parse::extract($filename);
            $order = $processor->matchReference($danfe);

            if (!$order) {
                continue;
            }

            $processor->setOrderDanfe($order, $danfe, $filename, $extensions);
        }
    }
}
