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
use AppBundle\Entity\Misc\AdditiveInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Manager\AdditiveManager;
use Exporter\Writer\CsvWriter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $orderColumnMapping = [
        'reference' => 'Orçamento',
        'status' => 'Status',
        'status_at' => 'Data status',
        'account' => 'Integrador',
        'cnpj' => 'CNPJ',
        'level' => 'Nível',
        'agent' => 'Atendente',
        'sub_orders' => 'Qte Sistemas',
        'power' => 'Potência total',
        'total_price' => 'Valor total',
        'shipping_type' => 'Tipo de frete',
        'shipping_price' => 'Valor do frete',
        'payment_method' => 'Condição pagamento',
        'delivery_at' => 'Disp. Coleta',
        'note' => 'Obs',
        'billing_name' => 'Nome faturamento',
        'billing_cnpj' => 'CNPJ faturamento',
        'invoices' => 'Num. NF',
        'billed_at' => 'Data faturamento'
    ];

    /**
     * @var array
     */
    private $suborderColumnMapping = [
        'reference' => 'Referência',
        'status' => 'Status',
        'status_at' => 'Data status',
        'account' => 'Integrador',
        'cnpj' => 'CNPJ',
        'level' => 'Nível do orçamento',
        'agent' => 'Atendente',
        'power' => 'Potência',
        'total_price' => 'Valor'
    ];

    /**
     * OrderExporter constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->collector = $container->get('component_collector');
        $this->storage = $container->get('app_storage');
        $this->manager = $container->get('order_manager');
        $this->addInsuranceColumns();
    }

    /**
     * @param $orders
     * @param $mode
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export($orders, $mode)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Sices Solar')
            ->setTitle('Orçamentos Filtrados');
        $projectRoot = $this->container->get('kernel')->getRootDir() . "/../..";
        $fileName = uniqid(md5(time())) . ".xlsx";
        $path = $projectRoot . "/.uploads/orders/export/" . $fileName;

        if ($mode === 1) {

            $ordersData = [];

            foreach ($orders as $order) {
                $ordersData[] = $this->extractOrderData($order);
            }

            $this->setSpreadsheetHeaders($spreadsheet, $this->orderColumnMapping);
            $this->setSpreadsheetData($spreadsheet, $ordersData);

        } elseif ($mode == 2) {

            $subordersData = [];

            foreach ($orders as $order) {
                $suborders = $order->getChildrens();

                foreach ($suborders as $suborder) {
                    $suborderData = $this->extractSuborderData($suborder);
                    $this->setInsuranceColumns($suborderData, $suborder);
                    $subordersData[] = $suborderData;
                }

                $this->setSpreadsheetHeaders($spreadsheet, $this->suborderColumnMapping);
                $this->setSpreadsheetData($spreadsheet, $subordersData);
            }
        }

        $writer = new Xlsx($spreadsheet);

        $writer->save($path);

        return $path;
    }

    /**
     * @param Order $order
     * @return array
     */
    private function extractOrderData(Order $order)
    {
        return [
            'reference' => $order->getReference(),
            'status' => $this->getStatusNameInPortuguese()[$order->getStatus()],
            'status_at' => $this->formatDate($order->getStatusAt()),
            'account' => $order->getAccount()->getFirstname(),
            'cnpj' => $order->getAccount()->getDocument(),
            'level' => $order->getLevel(),
            'agent' => $order->getAgent() ? $order->getAgent()->getFirstname() : '',
            'sub_orders' => count($order->getChildrens()),
            'power' => $order->getPower() . " kWp",
            'total_price' => $this->formatMoney($order->getTotal()),
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
    }

    /**
     * @param Order $order
     * @return array
     */
    private function extractSuborderData(Order $order)
    {
        $parent = $order->getParent();

        $data = [
            'reference' => $parent->getReference(),
            'status' => $this->getStatusNameInPortuguese()[$parent->getStatus()],
            'status_at' => $this->formatDate($parent->getStatusAt()),
            'account' => $parent->getAccount()->getFirstname(),
            'cnpj' => $parent->getAccount()->getDocument(),
            'level' => $order->getLevel(),
            'agent' => $parent->getAgent() ? $parent->getAgent()->getFirstname() : '',
            'power' => $order->getPower() . " kWp",
            'total_price' => $this->formatMoney($order->getTotal())
        ];

        return $data;
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param $data
     */
    private function setSpreadsheetData(Spreadsheet &$spreadsheet, $data) {
        try {

            $initialRow = 2;

            for ($i = 0; $i < count($data); $i++) {
                $column = 1;
                foreach ($data[$i] as $value) {
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($column, $initialRow, $value);
                    $column++;
                }
                $initialRow++;
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * @param Spreadsheet $spreadsheet
     */
    private function setSpreadsheetHeaders(Spreadsheet &$spreadsheet, $headers) {
        try {
            $i = 1;
            foreach ($headers as $column) {
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($i, 1, $column);
                $i++;
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * @deprecated
     * @param OrderInterface $order
     * @return string
     */
    public function exportLegacy(OrderInterface $order)
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
     * Set Insurance Columns
     */
    private function setInsuranceColumns(&$suborderData, $suborder) {

        /** @var AdditiveManager $additiveManager */
        $additiveManager = $this->container->get('additive_manager');

        $insurances = $additiveManager->findBy([
            'type' => AdditiveInterface::TYPE_INSURANCE
        ]);

        foreach ($insurances as $insurance) {
            $suborderData[$insurance->getName()] = $suborder->hasAdditive($insurance) ? "Sim" : "Não";
        }
    }

    /**
     * Add Insurance Columns
     */
    private function addInsuranceColumns()
    {
        /** @var AdditiveManager $additiveManager */
        $additiveManager = $this->container->get('additive_manager');

        $insurances = $additiveManager->findBy([
            'type' => AdditiveInterface::TYPE_INSURANCE
        ]);

        foreach ($insurances as $insurance) {
            $this->suborderColumnMapping['insurance_' . $insurance->getId()] = $insurance->getName();
        }
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
