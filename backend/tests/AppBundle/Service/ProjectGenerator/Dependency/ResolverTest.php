<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Dependency;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\Variety;
use AppBundle\Service\ProjectGenerator\Dependency\Loader;
use AppBundle\Service\ProjectGenerator\Dependency\Types;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class ResolverTest
 * @group project_generator
 * @group dependency_resolver
 */
class ResolverTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDefaultResolverByConfig()
    {
        $project = $this->createProject();

        /*$config = [
            ['code' => 'ABCDE'],
            ['code' => 'FGHIJ'],
            ['code' => 'KLMNO'],
            ['code' => 'PQRST'],
            ['code' => 'UVWXY']
        ];

        $manager = $this->manager('variety');

        foreach ($config as $key => $data){
            $variety = $manager->create();
            $data['description'] = sprintf('%s - Description', $data['code']);
            self::fluentSetters($variety, $data);
            $manager->save($variety);
            $config[$key]['id'] = $variety->getId();
        }

        $loader = new Loader($this->getContainer());

        $component = $loader->load($config[0]['id'], 'variety');

        $this->assertInstanceOf(Variety::class, $component);
        $this->assertNull($loader->load(0, Types::VARIETY));

        $settings = [
            [
                'type' => Types::VARIETY,
                'id' => $config[0]['id'],
                'quantity' => 3
            ],
            [
                'type' => Types::VARIETY,
                'id' => $config[1]['id'],
                'quantity' => 2
            ]
        ];*/

        //dump($mapping); die;*/
    }

    private function createProject()
    {
        $varieties = $this->createVarieties();
        $inverter = $this->createInverter();
    }

    private function createVarieties($total = 10)
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

        return $varieties;
    }

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
}
