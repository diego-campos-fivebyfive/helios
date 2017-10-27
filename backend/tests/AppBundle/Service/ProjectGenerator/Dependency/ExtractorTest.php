<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\ProjectStringBox;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Entity\Component\Structure;
use AppBundle\Service\ProjectGenerator\Dependency\Extractor;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\AppBundle\AppTestCase;

/**
 * Class ExtractorTest
 *
 * @group project_generator
 * @group dependency_extractor
 */
class ExtractorTest extends AppTestCase
{
    /**
     * Config mock and dependencies
     * @var array
     */
    private $config = [
        'inverter' => [
            'componentClass' => Inverter::class,
            'associationClass' => ProjectInverter::class,
            'projectGetter' => 'getProjectInverters',
            'componentGetter' => 'getInverter',
            'cycles' => 2,
            'quantity' => 2,
            'dependencies' => [
                ['type' => 'variety', 'id' => 100, 'ratio' => 3],
                ['type' => 'variety', 'id' => 300, 'ratio' => 10],
                ['type' => 'variety', 'id' => 400, 'ratio' => 2],
                ['type' => 'variety', 'id' => 300, 'ratio' => 5],
                ['type' => 'variety', 'id' => 400, 'ratio' => 7]
            ]
        ],

        'module' => [
            'componentClass' => Module::class,
            'associationClass' => ProjectModule::class,
            'projectGetter' => 'getProjectModules',
            'componentGetter' => 'getModule',
            'cycles' => 2,
            'quantity' => 5,
            'dependencies' => [
                ['type' => 'variety', 'id' => 555, 'ratio' => 3],
                ['type' => 'variety', 'id' => 333, 'ratio' => 10],
                ['type' => 'variety', 'id' => 444, 'ratio' => 2],
                ['type' => 'variety', 'id' => 355, 'ratio' => 5],
                ['type' => 'variety', 'id' => 450, 'ratio' => 7]
            ]
        ],

        'string_box' => [
            'componentClass' => StringBox::class,
            'associationClass' => ProjectStringBox::class,
            'projectGetter' => 'getProjectStringBoxes',
            'componentGetter' => 'getStringBox',
            'cycles' => 5,
            'quantity' => 3,
            'dependencies' => [
                ['type' => 'variety', 'id' => 555, 'ratio' => 3],
                ['type' => 'variety', 'id' => 333, 'ratio' => 10],
                ['type' => 'variety', 'id' => 444, 'ratio' => 2],
                ['type' => 'variety', 'id' => 355, 'ratio' => 5],
                ['type' => 'variety', 'id' => 450, 'ratio' => 7]
            ]
        ],

        'structure' => [
            'componentClass' => Structure::class,
            'associationClass' => ProjectStructure::class,
            'projectGetter' => 'getProjectStructures',
            'componentGetter' => 'getStructure',
            'cycles' => 5,
            'quantity' => 15,
            'dependencies' => [
                ['type' => 'variety', 'id' => 9000, 'ratio' => 3],
                ['type' => 'variety', 'id' => 1000, 'ratio' => 10],
                ['type' => 'variety', 'id' => 1000, 'ratio' => 2],
                ['type' => 'variety', 'id' => 355, 'ratio' => 5],
                ['type' => 'variety', 'id' => 450, 'ratio' => 7]
            ]
        ]
    ];

    /**
     * This method uses the information configured in the config property
     * to perform the automated extraction test process
     */
    public function testAssociationExtractor()
    {
        /** @var ProjectInterface $project */
        $project = $this->mockProject();

        $extractor = Extractor::create();

        $dependencies = $extractor->fromProject($project);

        $counters = [];
        foreach ($this->config as $family => $config) {
            foreach ($config['dependencies'] as $dependency) {
                if (!array_key_exists($dependency['id'], $counters))
                    $counters[$dependency['id']] = 0;

                $counters[$dependency['id']] += ($config['cycles'] * $config['quantity'] * $dependency['ratio']);
            }
        }

        foreach ($this->config as $family => $config){

            foreach ($dependencies['variety'] as $id => $ratio){
                $this->assertEquals($counters[$id], $ratio);
            }

            foreach ($config['dependencies'] as $dependency){
                $this->assertArrayHasKey($dependency['id'], $dependencies['variety']);
            }
        }

        $this->assertCount(count($counters), $dependencies['variety']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockProject()
    {
        $project = $this->getMockBuilder(Project::class)->getMock();

        foreach ($this->config as $type => $config) {

            $collection = new ArrayCollection();
            for ($i = 0; $i < $config['cycles']; $i++) {
                $collection->add($this->mockProjectComponent($type));
            }

            $project->method($config['projectGetter'])->willReturn($collection);
        }

        return $project;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockProjectComponent($type)
    {
        $component = $this->getMockBuilder($this->config[$type]['componentClass'])->getMock();

        $component
            ->method('getDependencies')
            ->willReturn($this->config[$type]['dependencies'])
        ;

        $projectInverter = $this->getMockBuilder($this->config[$type]['associationClass'])->getMock();
        $projectInverter->method('getQuantity')->willReturn($this->config[$type]['quantity']);
        $projectInverter->method($this->config[$type]['componentGetter'])->willReturn($component);

        return $projectInverter;
    }
}
