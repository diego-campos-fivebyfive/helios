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
use AppBundle\Entity\Order\Order;
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
     * @param Order $order
     */
    public function extractOrderData(Order $order)
    {
        $orderData = [
            'reference' => $order->getReference(),
            'status' => $this->getStatusNameInPortuguese()[$order->getStatus()],
            'send_at' => $this->formatDate($order->getSendAt()),
            'account' => $order->getAccount()->getName(),
            'cnpj' => $order->getCnpj(),
            'level' => $order->getLevel(),
            //'agent' => $order->getAgent()->getName(),
            'sub_orders' => count($order->getChildrens()),
            'power' => $order->getPower() . " kWp",
            'total' => $this->formatMoney($order->getTotal()),
            'shipping_type' => $order->getShippingRules()['type'],
            'shipping_price' => $this->formatMoney($order->getShipping()),
            'payment_method' => $order->getPaymentMethod('array')['name'],
            'delivery_at' => $this->formatDate($order->getDeliveryAt()),
            'note' => $order->getNote(),
            'billing_name' => $order->getBillingFirstname(),
            'billing_cnpj' => $order->getBillingCnpj(),
            'invoices' => implode(", ", $order->getInvoices()),
            'billed_at' => $this->formatDate($order->getBilledAt())
        ];

        return $orderData;
    }

    /**
     * @deprecated
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

            $data = $this->normalizeData($children, $key+1);

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
     * @deprecated
     * @param OrderInterface $order
     * @param $item
     * @return array
     */
    public function normalizeData(OrderInterface $order, $item)
    {
        $data = [
            'reference' => $order->getParent()->getReference(),
            'item' => $item >= 10 ? $item : sprintf('%02d', $item),
            'modules' => $this->countModules($order),
            'power' => $this->normalizePower($order),
            'power_initial' => $this->normalizePowerInitial($order),
            /*'module_maker_initial' => $this->normalizeElementMakerInitial($order, Element::FAMILY_MODULE),
            'inverter_maker_initial' => $this->normalizeElementMakerInitial($order, Element::FAMILY_INVERTER),
            'structure_type_initial' => 'M',*/
            'description' => $this->normalizeDescription($order)
        ];

        return $data;
    }

    /**
     * @return array
     */
    private function getStatusNameInPortuguese()
    {
        return [
            'Editando',
            'Pendente',
            'Validado',
            'Aprovado',
            'Cancelado',
            'Confirmado',
            'Em produção',
            'Coleta disponível',
            'Coletado',
            'Em trânsito',
            'Entregue'
        ];
    }

    /**
     * @param $money
     * @return mixed
     */
    private function formatMoney($money) {

        $formatedMoney = 'R$ '. number_format($money, 2);

        return str_replace('.',',', $formatedMoney);
    }

    /**
     * @param $date
     * @return mixed
     */
    private function formatDate($date)
    {
        return $date->format('d/m/Y');
    }

    /**
     * @deprecated
     * @param OrderInterface $order
     * @return float
     */
    private function normalizePower(OrderInterface $order)
    {
        $power = $order->getPower() > 1000 ? $order->getPower() / 1000 : $order->getPower();

        return round($power, 3);
    }

    /**
     * @deprecated
     * @param OrderInterface $order
     * @return string
     */
    private function normalizePowerInitial(OrderInterface $order)
    {
        return $order->getPower() > 1000 ? 'M' : 'K';
    }

    /**
     * @deprecated
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
     * @deprecated
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
     * @deprecated
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
     * @deprecated
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
     * @deprecated
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
