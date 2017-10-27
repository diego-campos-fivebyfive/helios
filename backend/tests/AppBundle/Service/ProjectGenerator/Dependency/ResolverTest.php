<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectVariety;
use AppBundle\Entity\Component\Variety;
use AppBundle\Service\ProjectGenerator\Dependency\Extractor;
use AppBundle\Service\ProjectGenerator\Dependency\Resolver;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class ResolverTest
 * @group project_generator
 * @group dependency_resolver
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class ResolverTest extends AppTestCase
{
    use ObjectHelperTest;

    /**
     * Default Test
     * Default process of binding dependencies
     */
    public function testResolverWithNonExistentRelationship()
    {
        $varieties = $this->createVarieties(15);
        $inverter = $this->createInverter();

        $this->addDependencies($inverter, $varieties);

        $manager = $this->manager('project');

        $project = $manager->create();

        $projectInverter = new ProjectInverter();
        $projectInverter
            ->setProject($project)
            ->setInverter($inverter)
            ->setQuantity(1)
        ;

        $this->assertCount(0, $project->getProjectVarieties()->toArray());

        Resolver::create($this->getContainer())->resolve($project);

        $dependencies = Extractor::create()->fromProject($project);

        $this->assertEquals(count($dependencies['variety']), $project->getProjectVarieties()->count());

        $manager->save($project);
    }

    /**
     * Cumulative Test
     * Handle the pre-existence of the component defined as dependency
     */
    public function testResolverWithExistentRelationship()
    {
        $varieties = $this->createVarieties(15);
        $inverter = $this->createInverter();

        $this->addDependencies($inverter, $varieties);

        $manager = $this->manager('project');

        $project = $manager->create();

        $projectInverter = new ProjectInverter();
        $projectInverter
            ->setProject($project)
            ->setInverter($inverter)
            ->setQuantity(1)
        ;

        // ADD VARIETY DEPENDENCIES FOR CUMULATIVE TEST
        $count = 0;
        for ($i = 10; $i < count($varieties); $i++){
            $projectVariety = new ProjectVariety();
            $projectVariety
                ->setProject($project)
                ->setQuantity($i)
                ->setVariety($varieties[$i])
            ;
            $count++;
        }

        // Test if pre-configured varieties is attached
        $this->assertCount($count, $project->getProjectVarieties()->toArray());

        Resolver::create($this->getContainer())->resolve($project);

        $dependencies = Extractor::create()->fromProject($project);

        $this->assertEquals(count($dependencies['variety']), $project->getProjectVarieties()->count());

        $manager->save($project);
    }

    /**
     * Promotional Test
     * Ignores non-promotional components when the project is promotional
     */
    public function testResolverWithPromotional()
    {
        $total = 15;
        $promo = 5;
        $varieties = $this->createVarieties($total, $promo);
        $inverter = $this->createInverter();

        $this->addDependencies($inverter, $varieties);

        $manager = $this->manager('project');

        $project = $manager->create();

        // Initialize default for promotional detection
        $project->setDefaults([
            'is_promotional' => true,
            'latitude' => 0,
            'longitude' => 0,
            'power' => 0,
            'consumption' => 0
        ]);

        // Test project is promotional
        $this->assertTrue($project->isPromotional());

        $projectInverter = new ProjectInverter();
        $projectInverter
            ->setProject($project)
            ->setInverter($inverter)
            ->setQuantity(1)
        ;

        $this->assertCount(0, $project->getProjectVarieties()->toArray());

        Resolver::create($this->getContainer())->resolve($project);

        // Test if only promotional varieties is attached
        $this->assertCount($promo, $project->getProjectVarieties()->toArray());
    }

    /**
     * @param int $total
     * @return array
     */
    private function createVarieties($total = 10, $promo = 0)
    {
        $manager = $this->manager('variety');
        $varieties = [];
        for ($i = 0; $i < $total; $i++) {
            $variety = $manager->create();
            $variety
                ->setType(Variety::TYPE_CABLE)
                ->setSubtype('foo_bar')
                ->setCode(self::randomString(10))
                ->setDescription(self::randomString(100))
            ;

            $manager->save($variety);

            $varieties[] = $variety;
        }

        for ($i = 0; $i < $promo; $i++){
            $varieties[$i]->setPromotional(true);
        }

        return $varieties;
    }

    /**
     * @return mixed|object
     */
    private function createInverter()
    {
        $manager = $this->manager('inverter');

        $inverter = $manager->create();

        $inverter
            ->setCode(self::randomString(10))
            ->setDescription(self::randomString(100))
        ;

        $manager->save($inverter);

        return $inverter;
    }

    /**
     * @param Inverter $inverter
     * @param array $varieties
     */
    private function addDependencies(Inverter $inverter, array $varieties)
    {
        $dependencies = [];
        foreach ($varieties as $variety){
            $dependencies[] = [
                'type' => 'variety',
                'id' => $variety->getId(),
                'ratio' => 1
            ];
        }

        $inverter->setDependencies($dependencies);

        $this->manager('inverter')->save($inverter);

        $this->assertCount(count($dependencies), $inverter->getDependencies());
    }
}
