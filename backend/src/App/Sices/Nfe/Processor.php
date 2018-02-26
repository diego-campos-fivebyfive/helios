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

class Processor
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * Processor constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;
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
     * @param Order $order
     * @param $danfe
     * @param $filename
     * @param $extensions
     * @return array
     */
    public function setOrderDanfe(Order $order, $danfe, $filename, $extensions)
    {
        $order->addInvoice($danfe['invoice']);

        foreach ($extensions as $extension) {
            $file = "{$filename}.{$extension}";
            $this->addFileName($order,$file,$extension);
        }

        if ($danfe['billing'] == 'S') {
            $date = $this->formatBilledAt($danfe['billed_at']);
            $order->setBilledAt($date);
        }
        $this->manager->save($order);
    }

    /**
     * @param Order $order
     * @param $filename
     * @param $extension
     */
    private function addFileName(Order $order, $filename, $extension)
    {
            if ($extension == 'pdf') {
                $order->addFile('nfe_pdf', $filename);
            }

            if ($extension == 'xml') {
                $order->addFile('nfe_xml', $filename);
            }
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
}
