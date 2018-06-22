<?php

namespace Tests\AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Manager\InverterManager;
use AppBundle\Manager\ModuleManager;
use AppBundle\Service\ProjectGenerator\Resolver\SolarEdgeResolver;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group project_generator_optimizer
 */
class OptimizerTest extends WebTestCase
{
    public function testOptimizer()
    {
        $manager = $this->getContainer()->get('project_manager');

        /** @var ModuleManager $moduleManager */
        $moduleManager = $this->getContainer()->get('module_manager');

        $module = $moduleManager->find(46169);

        /** @var ProjectInterface $project */
        $project = $manager->create();

        $projectModule = new ProjectModule();
        $projectModule
            ->setProject($project)
            ->setModule($module)
            ->setQuantity(100)
        ;

        /** @var InverterManager $inverterManager */
        $inverterManager = $this->getContainer()->get('inverter_manager');

        $inverter = $inverterManager->find(6508);

        $projectInverter = new ProjectInverter();
        $projectInverter
            ->setProject($project)
            ->setInverter($inverter)
            ->setQuantity(1)
        ;

        self::assertEquals(0, count($project->getProjectVarieties()));

        $isSolarEdge = (bool) preg_match(SolarEdgeResolver::SOLAR_EDGE_EXPRESSION, $inverter->getDescription());

        self::assertTrue($isSolarEdge);

        $solarEdgeResolver = new SolarEdgeResolver($this->getContainer());

        $solarEdgeResolver->resolve($project);

        self::assertEquals(1, count($project->getProjectVarieties()));

        $varietyProject = $project->getProjectVarieties();

        self::assertEquals(50, $varietyProject[0]->getQuantity());
    }

    public function testNotOptimizer()
    {
        $manager = $this->getContainer()->get('project_manager');

        /** @var ModuleManager $moduleManager */
        $moduleManager = $this->getContainer()->get('module_manager');

        $module = $moduleManager->find(46169);

        /** @var ProjectInterface $project */
        $project = $manager->create();

        $projectModule = new ProjectModule();
        $projectModule
            ->setProject($project)
            ->setModule($module)
            ->setQuantity(100)
        ;

        /** @var InverterManager $inverterManager */
        $inverterManager = $this->getContainer()->get('inverter_manager');

        $inverter = $inverterManager->find(6509);

        $projectInverter = new ProjectInverter();
        $projectInverter
            ->setProject($project)
            ->setInverter($inverter)
            ->setQuantity(1)
        ;

        self::assertEquals(0, count($project->getProjectVarieties()));

        $isSolarEdge = (bool) preg_match(SolarEdgeResolver::SOLAR_EDGE_EXPRESSION, $inverter->getDescription());

        self::assertFalse($isSolarEdge);

        $solarEdgeResolver = new SolarEdgeResolver($this->getContainer());

        $solarEdgeResolver->resolve($project);

        self::assertEquals(0, count($project->getProjectVarieties()));

        $varietyProject = $project->getProjectVarieties();

        self::assertNull($varietyProject[0]);
    }
}
