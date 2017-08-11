<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\ProjectStringBox;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Component\Variety;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Service\Order\OrderTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderTransformerTest
 * @group order_transformer
 */
class OrderTransformerTest extends AppTestCase
{
    public function testTransformFromProject()
    {
        $project = $this->createProject();

        $this->assertCount(1, $project->getProjectInverters()->toArray());

        $orderTransformer = $this->getContainer()->get('order_transformer');

        $order = $orderTransformer->transformFromProject($project);

        $this->assertInstanceOf(OrderInterface::class, $order);
        $this->assertCount(5, $order->getElements()->toArray());
        $this->assertNotNull($order->getId());
        $this->assertEquals(16300, $order->getTotal());
    }

    private function createProject()
    {
        // Module
        $moduleMock = $this->getMockBuilder(Inverter::class)->getMock();
        $moduleMock->method('getCode')->willReturn('MOD001');
        $moduleMock->method('getModel')->willReturn('Module A');

        // Inverter
        $inverterMock = $this->getMockBuilder(Inverter::class)->getMock();
        $inverterMock->method('getCode')->willReturn('INV001');
        $inverterMock->method('getModel')->willReturn('Inverter A');

        // StringBox
        $stringBoxMock = $this->getMockBuilder(StringBox::class)->getMock();
        $stringBoxMock->method('getCode')->willReturn('STRB001');
        $stringBoxMock->method('getDescription')->willReturn('String Box A');

        // Structure
        $structureMock = $this->getMockBuilder(Structure::class)->getMock();
        $structureMock->method('getCode')->willReturn('STRR001');
        $structureMock->method('getDescription')->willReturn('Structure A');

        // Variety
        $varietyMock = $this->getMockBuilder(Variety::class)->getMock();
        $varietyMock->method('getCode')->willReturn('VAR001');
        $varietyMock->method('getDescription')->willReturn('Variety A');

        // ProjectModule
        $projectModuleMock = $this->getMockBuilder(ProjectModule::class)->getMock();
        $projectModuleMock->method('getQuantity')->willReturn(10);
        $projectModuleMock->method('getUnitCostPrice')->willReturn(1000);
        $projectModuleMock->method('getModule')->willReturn($moduleMock);

        // ProjectInverter
        $projectInverterMock = $this->getMockBuilder(ProjectInverter::class)->getMock();
        $projectInverterMock->method('getQuantity')->willReturn(5);
        $projectInverterMock->method('getUnitCostPrice')->willReturn(1000);
        $projectInverterMock->method('getInverter')->willReturn($inverterMock);

        // ProjectStringBox
        $projectStringBoxMock = $this->getMockBuilder(ProjectStringBox::class)->getMock();
        $projectStringBoxMock->method('getQuantity')->willReturn(2);
        $projectStringBoxMock->method('getUnitCostPrice')->willReturn(100);
        $projectStringBoxMock->method('getStringBox')->willReturn($stringBoxMock);

        // ProjectStructure
        $projectStructureMock = $this->getMockBuilder(ProjectStructure::class)->getMock();
        $projectStructureMock->method('getQuantity')->willReturn(10);
        $projectStructureMock->method('getUnitCostPrice')->willReturn(100);
        $projectStructureMock->method('getStructure')->willReturn($structureMock);

        // ProjectVariety
        $projectVarietyMock = $this->getMockBuilder(ProjectVariety::class)->getMock();
        $projectVarietyMock->method('getQuantity')->willReturn(10);
        $projectVarietyMock->method('getUnitCostPrice')->willReturn(10);
        $projectVarietyMock->method('getVariety')->willReturn($varietyMock);

        // Project
        $projectMock = $this->getMockBuilder(Project::class)->getMock();

        // Initialize Mock Collections
        $projectMock->method('getProjectInverters')->willReturn(
            new ArrayCollection([$projectInverterMock])
        );

        $projectMock->method('getProjectModules')->willReturn(
            new ArrayCollection([$projectModuleMock])
        );

        $projectMock->method('getProjectStringBoxes')->willReturn(
            new ArrayCollection([$projectStringBoxMock])
        );

        $projectMock->method('getProjectStructures')->willReturn(
            new ArrayCollection([$projectStructureMock])
        );

        $projectMock->method('getProjectVarieties')->willReturn(
            new ArrayCollection([$projectVarietyMock])
        );

        $member = $this->getFixture('member');

        $projectMock->method('getMember')->willReturn($member);

        return $projectMock;
    }
}