<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\OrderInterface;
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

    public function testDefaultServiceScenario()
    {
        // Configure Module
        $module = $this->configureModule([
            'code' => self::randomString(25),
            'maker' => $this->createMakerModule('Module Maker'),
            'max_power' => 500
        ]);

        // Configure Inverter
        $inverter = $this->configureInverter([
            'code' => self::randomString(15),
            'maker' =>  $this->createMakerInverter('Inverter Maker')
        ]);

        // Configure Structure
        $structure = $this->configureStructure([
            'code' => self::randomString(18),
            'maker' =>  $this->createMakerInverter('Structure Maker')
        ]);

        $manager = $this->manager('order');

        /** @var OrderInterface $children */
        $children = $manager->create();

        // Add Module
        $element = new Element();
        ElementResolver::resolve($element, $module);
        $children->addElement($element);

        // Add Inverter
        $element2 = new Element();
        ElementResolver::resolve($element2, $inverter);
        $children->addElement($element2);

        // Add Structure
        $element3 = new Element();
        ElementResolver::resolve($element3, $structure);
        $children->addElement($element3);

        OrderManipulator::checkPower($children);

        $this->assertEquals(0.5, $children->getPower());

        /** @var OrderInterface $order */
        $order = $manager->create();

        $order->addChildren($children);

        $manager->save($order);

        $this->service('order_reference')->generate($order);

        $exporter = $this->getOrderExporter();

        $data = $exporter->normalizeData($children, 2);

        $this->assertCount(6,$data);
        $this->assertEquals('02', $data['item']);
        $this->assertArrayHasKey('power', $data);
        $this->assertNotNull($order->getReference());
        $this->assertEquals($order->getReference(), $data['reference']);
        $this->assertEquals(1, $data['modules']);
        $this->assertEquals(0.500, $data['power']);
        $this->assertEquals('K', $data['power_initial']);
        /*$this->assertEquals('M', $data['module_maker_initial']);
        $this->assertEquals('I', $data['inverter_maker_initial']);
        $this->assertEquals('M', $data['structure_type_initial']);*/

        $exporter->export($order);
    }

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
