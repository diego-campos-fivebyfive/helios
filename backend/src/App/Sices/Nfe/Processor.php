<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace App\Sices\Nfe;

use AppBundle\Entity\Order\Order;
use AppBundle\Manager\OrderManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Processor
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Processor constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $container->get('order_manager');
    }

    /**
     * @param array $files
     * @return mixed
     */
    public function indexer(array $files)
    {
        return array_reduce($files, function ($carry, $file) {
            if (!strpos($file, '.')) {
                return $carry;
            }

            list($name, $extension) = explode('.', $file);
            $carry[$name][] = $extension;
            return $carry;
        }, []);
    }

    /**
     * @param $danfe
     * @return null|object
     */
    public function matchReference($danfe)
    {
        return $this->manager->findOneBy([
            'reference' => $danfe['reference']
        ]);
    }

    /**
     * @param $filesList
     * @param $path
     */
    public function pushS3($filesList, $path)
    {
        foreach ($filesList as $filname) {
            $file = "{$path}/{$filname}";
            $options = $this->getS3Options($filname);
            $this->container->get('app_storage')->push($options, $file);
        }
    }

    /**
     * @param Order $order
     * @param $danfe
     * @param $filename
     * @param $extensions
     */
    public function setOrderDanfe(Order $order, $danfe, $filename, $extensions)
    {
        $order->addInvoice($danfe['invoice']);

        foreach ($extensions as $extension) {
            $file = "{$filename}.{$extension}";
            if ($order->hasFile('nfe', $file)) {
                continue;
            }
            $order->addFile('nfe', $file);
        }

        if ($danfe['billing'] == 'S') {
            $date = $this->formatBilledAt($danfe['billed_at']);
            $order->setBilledAt($date);
        }

        $this->manager->save($order);
    }

    /**
     * @param $billedAt
     * @return \DateTime
     */
    private function formatBilledAt($billedAt)
    {
        $year = substr($billedAt, 0,-4);
        $month = substr($billedAt, 4,-2);
        $day = substr($billedAt, 6, 2);

        return new \DateTime("${year}-${month}-${day}");
    }

    /**
     * @param $filename
     * @return array
     */
    private function getS3Options($filename)
    {
        return [
            'filename' => $filename,
            'root' => 'fiscal',
            'type' => 'danfe',
            'access' => 'private'
        ];
    }

}
