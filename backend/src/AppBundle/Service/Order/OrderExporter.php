<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Order;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\OrderInterface;
use Exporter\Writer\CsvWriter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrderExporter
 * This class is a util export service for csv order, used in protheus crm
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderExporter
{
    /**
     * @var string
     */
    private $enclosure = '"';

    /**
     * @var \AppBundle\Service\Component\FileHandler
     */
    private $storage;

    /**
     * @var ComponentCollector
     */
    private $collector;

    /**
     * @var \AppBundle\Manager\OrderManager
     */
    private $manager;

    /**
     * OrderExporter constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->collector = $container->get('component_collector');
        $this->storage = $container->get('app_storage');
        $this->manager = $container->get('order_manager');
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    public function export(OrderInterface $order)
    {
        $reference = $order->getReference();

        $options = array(
            'id' => $reference,
            'root' => 'order',
            'type' => 'order',
            'filename' => sprintf('%s.csv', $reference),
            'access' => 'private'
        );

        $filename = $this->storage->location($options);

        $this->prepareFilePath($filename);

        $writer = new CsvWriter($filename, ';', $this->enclosure, '\\', false);

        $writer->open();

        /**
         * @var int $key
         * @var OrderInterface $children
         */
        foreach ($order->getChildrens() as $key => $children) {

            $data = $this->normalizeData($children);

            foreach ($children->getElements() as $element) {

                $unitPrice = round($element->getUnitPrice(), 3);

                $data = array_merge($data, [
                    'code' => $element->getCode(),
                    'quantity' => $element->getQuantity(),
                    'unit_price' => $unitPrice
                ]);

                $writer->write($data);
            }
        }

        // Remove enclosure string
        $content = str_replace($this->enclosure, '', file_get_contents($filename));
        file_put_contents($filename, $content);

        $this->storage->push($options, $filename);

        $order->setFileExtract($options['filename']);

        $this->manager->save($order);

        return $filename;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function normalizeData(OrderInterface $order)
    {
        $data = [
            'reference' => $order->getParent()->getReference(),
            'modules' => $this->countModules($order),
            'power' => $this->normalizePower($order),
            'power_initial' => $this->normalizePowerInitial($order),
            'module_maker_initial' => $this->normalizeElementMakerInitial($order, Element::FAMILY_MODULE),
            'inverter_maker_initial' => $this->normalizeElementMakerInitial($order, Element::FAMILY_INVERTER),
            'structure_maker_initial' => $this->normalizeElementMakerInitial($order, Element::FAMILY_STRUCTURE),
            'description' => $this->normalizeDescription($order)
        ];

        return $data;
    }

    /**
     * @param OrderInterface $order
     * @return float
     */
    private function normalizePower(OrderInterface $order)
    {
        $power = $order->getPower() > 1000 ? $order->getPower() / 1000 : $order->getPower();

        return round($power, 3);
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    private function normalizePowerInitial(OrderInterface $order)
    {
        return $order->getPower() > 1000 ? 'M' : 'K';
    }

    /**
     * @param OrderInterface $order
     * @param $family
     * @return string
     */
    private function normalizeElementMakerInitial(OrderInterface $order, $family)
    {
        if (null != $element = $order->getElements($family)->first()) {

            $component = $this->collector->fromCode($element->getCode());

            if ($component instanceof ComponentInterface)
                return $this->normalizeComponentMakerInitial($component);
        }

        return '';
    }

    /**
     * @param ComponentInterface $component
     * @return string
     */
    private function normalizeComponentMakerInitial(ComponentInterface $component)
    {
        $maker = $component->getMaker();
        $initial = '';

        if ($maker instanceof MakerInterface)
            $initial = strtoupper(substr($maker->getName(), 0, 1));

        return $initial;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    private function normalizeDescription(OrderInterface $order)
    {
        $power = $order->getPower();
        $label = $power > 1000 ? 'M' : 'K';

        $format = 'SISTEMA FV SICES CONECTADO A REDE %s%sW';

        return sprintf(
            $format,
            number_format(($power > 1000 ? $power / 1000 : $power), 3, ',', '.'),
            $label
        );
    }

    /**
     * @param OrderInterface $order
     * @return int
     */
    private function countModules(OrderInterface $order)
    {
        $count = 0;
        foreach ($order->getElements(Element::FAMILY_MODULE) as $element) {
            $count += $element->getQuantity();
        }

        return $count;
    }

    /**
     * @param $filename
     */
    private function prepareFilePath($filename)
    {
        if (is_file($filename))
            unlink($filename);

        $dir = substr($filename, 0, strrpos($filename, '/')+1);

        if(!is_dir($dir)) mkdir($dir);
    }
}
