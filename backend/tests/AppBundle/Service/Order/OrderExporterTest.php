<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Manager\AccountManager;
use AppBundle\Model\Document\Account;
use AppBundle\Service\Order\ElementResolver;
use AppBundle\Service\Order\OrderExporter;
use AppBundle\Service\Order\OrderManipulator;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class OrderExporterTest
 * @group order_exporter
 */
class OrderExporterTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testExtractOrderData()
    {
        $orderManager = $this->getContainer()->get('order_manager');
        /** @var OrderExporter $orderExporter */
        $orderExporter = $this->getContainer()->get('order_exporter');
        $accountManager = $this->getContainer()->get('account_manager');

        /** @var AccountInterface $account */
        $account = $accountManager->create();

        $account->setFirstname('Conta');
        $account->setLevel('Partner');

        $account->setContext('account');

        $accountManager->save($account);

        /** @var Order $order */
        $order = $orderManager->create();

        $order->setReference('12345');
        $order->setAccount($account);
        $order->setStatus(3);
        $order->getAccount()->setDocument('3333');
        $order->setPower('20');
        $order->addInvoice('123');
        $order->addInvoice('456');
        $order->setMetadata([
            'payment_method' => [
                'name' => 'dinheiro',
                'enabled' => true
            ]
        ]);
        $order->setNote('nota');
        $order->setShippingRules([
            'type' => 'sices',
        ]);
        $order->setBilledAt(new \DateTime());
        $order->setDeliveryAt(new \DateTime());
        $order->setBillingFirstname('Consumidor');
        $order->setBillingCnpj('54321');

        $subOrder1 = $orderManager->create();
        $subOrder2 = $orderManager->create();

        $element = new Element();
        $element->setUnitPrice(10);
        $element->setQuantity(2);

        $order->addChildren($subOrder1);
        $order->addChildren($subOrder2);

        $subOrder1->addElement($element);
        $subOrder1->setPower(10);

        $subOrder2->addElement($element);
        $subOrder2->setPower(2);

        $orderManager->save($order);

        $orderData = $orderExporter->extractOrderData($order);

        $this->assertEquals($orderData['reference'], '12345');
        $this->assertEquals($orderData['status'], 'Aprovado');
        $this->assertEquals($orderData['status_at'], (new \DateTime())->format('d/m/Y'));
        $this->assertEquals($orderData['account'], 'Conta');
        $this->assertEquals($orderData['cnpj'], '3333');
        $this->assertEquals($orderData['level'], 'Partner');
        $this->assertEquals($orderData['sub_orders'], 2);
        $this->assertEquals($orderData['power'], '12 kWp');
        $this->assertEquals($orderData['total_price'], 'R$ 40,00');
        $this->assertEquals($orderData['shipping_type'], 'sices');
        $this->assertEquals($orderData['shipping_price'], 'R$ 0,00');
        $this->assertEquals($orderData['payment_method'], 'dinheiro');
        $this->assertEquals($orderData['note'], 'nota');
        $this->assertEquals($orderData['billing_name'], 'Consumidor');
        $this->assertEquals($orderData['billing_cnpj'], '54321');
        $this->assertEquals($orderData['invoices'], '123, 456');

        $suborderData = $orderExporter->extractSuborderData($subOrder1);

        $this->assertEquals($suborderData['reference'], '12345');
        $this->assertEquals($suborderData['status'], 'Aprovado');
        $this->assertEquals($suborderData['status_at'], (new \DateTime())->format('d/m/Y'));
        $this->assertEquals($suborderData['account'], 'Conta');
        $this->assertEquals($suborderData['cnpj'], '3333');
        $this->assertEquals($suborderData['level'], 'Partner');
        $this->assertEquals($suborderData['power'], '10 kWp');
        $this->assertEquals($suborderData['total_price'], 'R$ 20,00');
    }

//    public function testDefaultServiceScenario()
//    {
//        // Configure Module
//        $module = $this->configureModule([
//            'code' => self::randomString(25),
//            'maker' => $this->createMakerModule('Module Maker'),
//            'max_power' => 500
//        ]);
//
//        // Configure Inverter
//        $inverter = $this->configureInverter([
//            'code' => self::randomString(15),
//            'maker' =>  $this->createMakerInverter('Inverter Maker')
//        ]);
//
//        // Configure Structure
//        $structure = $this->configureStructure([
//            'code' => self::randomString(18),
//            'maker' =>  $this->createMakerInverter('Structure Maker')
//        ]);
//
//        $manager = $this->manager('order');
//
//        /** @var OrderInterface $children */
//        $children = $manager->create();
//
//        // Add Module
//        $element = new Element();
//        ElementResolver::resolve($element, $module);
//        $children->addElement($element);
//
//        // Add Inverter
//        $element2 = new Element();
//        ElementResolver::resolve($element2, $inverter);
//        $children->addElement($element2);
//
//        // Add Structure
//        $element3 = new Element();
//        ElementResolver::resolve($element3, $structure);
//        $children->addElement($element3);
//
//        OrderManipulator::checkPower($children);
//
//        $this->assertEquals(0.5, $children->getPower());
//
//        /** @var OrderInterface $order */
//        $order = $manager->create();
//
//        $order->addChildren($children);
//
//        $manager->save($order);
//
//        $this->service('order_reference')->generate($order);
//
//        $exporter = $this->getOrderExporter();
//
//        $data = $exporter->normalizeData($children, 2);
//
//        $this->assertCount(6,$data);
//        $this->assertEquals('02', $data['item']);
//        $this->assertArrayHasKey('power', $data);
//        $this->assertNotNull($order->getReference());
//        $this->assertEquals($order->getReference(), $data['reference']);
//        $this->assertEquals(1, $data['modules']);
//        $this->assertEquals(0.500, $data['power']);
//        $this->assertEquals('K', $data['power_initial']);
//        /*$this->assertEquals('M', $data['module_maker_initial']);
//        $this->assertEquals('I', $data['inverter_maker_initial']);
//        $this->assertEquals('M', $data['structure_type_initial']);*/
//
//        $exporter->export($order);
//    }

    /**
     * @param $name
     * @return MakerInterface
     */
    private function createMakerModule($name)
    {
        return $this->createMaker($name, MakerInterface::CONTEXT_MODULE);
    }

    /**
     * @param $name
     * @return MakerInterface
     */
    private function createMakerInverter($name)
    {
        return $this->createMaker($name, MakerInterface::CONTEXT_INVERTER);
    }

    /**
     * @param $name
     * @param $context
     * @return MakerInterface
     */
    private function createMaker($name, $context)
    {
        $manager = $this->manager('maker');

        /** @var MakerInterface $maker */
        $maker = $manager->create();

        $maker
            ->setName($name)
            ->setContext($context)
            ->setEnabled(true);

        $manager->save($maker);

        return $maker;
    }

    /**
     * @param array $definitions
     * @return ComponentInterface|object
     */
    private function configureModule(array $definitions = [])
    {
        return $this->configureComponent('module', $definitions);
    }

    /**
     * @param array $definitions
     * @return ComponentInterface|object
     */
    private function configureInverter(array $definitions = [])
    {
        return $this->configureComponent('inverter', $definitions);
    }

    /**
     * @param array $definitions
     * @return ComponentInterface|object
     */
    private function configureStructure(array $definitions = [])
    {
        return $this->configureComponent('structure', $definitions);
    }

    /**
     * @param $family
     * @param array $definitions
     * @return object|ComponentInterface
     */
    private function configureComponent($family, array $definitions = [])
    {
        $component = $this->getFixture($family);

        $this->applyComponentDefinitions($component, $definitions);

        $this->manager($family)->save($component);

        return $component;
    }

    /**
     * @param $component
     * @param array $definitions
     */
    private function applyComponentDefinitions($component, array $definitions = [])
    {
        foreach ($definitions as $definition => $value){
            $this->accessor->setValue($component, $definition, $value);
        }
    }

    /**
     * @return OrderExporter|object
     */
    private function getOrderExporter()
    {
        return $this->service('order_exporter');
    }
}
