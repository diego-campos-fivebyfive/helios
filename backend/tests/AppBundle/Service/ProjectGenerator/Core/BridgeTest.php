<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\ProjectStructure;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Entity\Component\Structure;
use AppBundle\Manager\StringBoxManager;
use AppBundle\Service\ProjectGenerator\Core\Bridge;
use Tests\AppBundle\AppTestCase;

/**
 * @group generator_bridge
 */
class BridgeTest extends AppTestCase
{
    public function testBridge()
    {
        $manager = $this->manager('project');

        /** @var Module $module */
        $module = new Module();
        $module->setMaxPower(280);
        $module->setVoltageMaxPower(30.5);
        $module->setOpenCircuitVoltage(40.5);
        $module->setShortCircuitCurrent(9.45);
        $module->setTempCoefficientVoc(-0.31);

        $moduleManager = $this->manager('module');
        $moduleManager->save($module);

        $project = $manager->create();

        $projectModule = new ProjectModule();
        $projectModule
            ->setProject($project)
            ->setModule($module)
            ->setQuantity(20)
        ;

        $makerManager = $this->manager('maker');
        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Novo Maker');
        $maker->setEnabled(1);
        $maker->setContext('inverter');
        $makerManager->save($maker);

        /** @var Maker $maker */
        $maker2 = $makerManager->create();
        $maker2->setName('Maker stringBox');
        $maker2->setEnabled(1);
        $maker2->setContext('string_box');
        $makerManager->save($maker2);

        $inverterManager = $this->manager('inverter');

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter->setDescription('desc');
        $inverter->setCode(123);
        $inverter->setGeneratorLevels(["partner","promotional","finame"]);
        $inverter->setPhases(1);
        $inverter->setPhaseVoltage(220);
        $inverter->setCompatibility(2);
        $inverter->setNominalPower(5.5);
        $inverter->setMinPowerSelection(0);
        $inverter->setMaxPowerSelection(18);
        $inverter->setMpptParallel(true);
        $inverter->setMpptNumber(2);
        $inverter->setMpptMin(300);
        $inverter->setInProtection(false);
        $inverter->setMaxDcVoltage(900);
        $inverter->setMpptMaxDcCurrent(18.9);
        $inverter->setMaker($maker);
        $inverterManager->save($inverter);

        /** @var StringBoxManager $stringBoxManager */
        $stringBoxManager = $this->manager('string_box');

        /** @var StringBox $stringBox */
        $stringBox = $stringBoxManager->create();
        $stringBox->setDescription('desc 1');
        $stringBox->setCode(123);
        $stringBox->setMaker($maker2);
        $stringBox->setInputs(2);
        $stringBox->setOutputs(3);
        $stringBox->setGeneratorLevels(["partner","promotional","finame"]);
        $stringBoxManager->save($stringBox);

        $project->setDefaults([
            'level' => 'partner',
            'fdi_min' => 0.75,
            'fdi_max' => 1.3,
            'power' => 18,
            'voltage' => 220,
            'phases' => 1,
            'inverter_maker' => $maker->getId(),
            'string_box_maker' => $maker2->getId(),
            'latitude' => 0,
            'longitude' => 0,
            'consumption' => 0
        ]);

        $manager->save($project);

        /** @var Bridge $bridge */
        $bridge = $this->getContainer()->get('generator_bridge');

        $this->createStructures();
        // TODO: retorno do "Core::process($parameters)" do metodo resolve
        $result = $bridge->resolve($project);

//        self::assertEquals(4, count($result));
//        self::assertEquals(2, $result['inverters'][0]['id']);
    }

    public function testGroups()
    {
        $manager = $this->manager('project');

        /** @var Module $module */
        $module = new Module();
        $module->setMaxPower(280);
        $module->setVoltageMaxPower(30.5);
        $module->setOpenCircuitVoltage(40.5);
        $module->setShortCircuitCurrent(9.45);
        $module->setTempCoefficientVoc(-0.31);

        $moduleManager = $this->manager('module');
        $moduleManager->save($module);

        $project1 = $manager->create();
        $project2 = $manager->create();
        $project3 = $manager->create();

        $projectModule1 = new ProjectModule();
        $projectModule1
            ->setProject($project1)
            ->setModule($module)
            ->setQuantity(2728)
            ->setGroups([
                248,
                248,
                248,
                248,
                248,
                248,
                248,
                248,
                248,
                248,
                248
            ])
        ;

        $projectModule2 = new ProjectModule();
        $projectModule2
            ->setProject($project2)
            ->setModule($module)
            ->setQuantity(2480)
        ;

        $projectModule3 = new ProjectModule();
        $projectModule3
            ->setProject($project3)
            ->setModule($module)
            ->setQuantity(2480)
            ->setGroups([
                248,
                248,
                248,
                248,
                248,
                248,
                248,
                248,
                248,
                248
            ])
        ;

        $makerManager = $this->manager('maker');
        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Novo Maker');
        $maker->setEnabled(1);
        $maker->setContext('inverter');
        $makerManager->save($maker);

        /** @var Maker $maker */
        $maker2 = $makerManager->create();
        $maker2->setName('Maker stringBox');
        $maker2->setEnabled(1);
        $maker2->setContext('string_box');
        $makerManager->save($maker2);

        $inverterManager = $this->manager('inverter');

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter->setDescription('desc');
        $inverter->setCode(123);
        $inverter->setGeneratorLevels(["partner","promotional","finame"]);
        $inverter->setPhases(1);
        $inverter->setPhaseVoltage(220);
        $inverter->setCompatibility(2);
        $inverter->setNominalPower(5.5);
        $inverter->setMinPowerSelection(0);
        $inverter->setMaxPowerSelection(18);
        $inverter->setMpptParallel(true);
        $inverter->setMpptNumber(2);
        $inverter->setMpptMin(300);
        $inverter->setInProtection(false);
        $inverter->setMaxDcVoltage(900);
        $inverter->setMpptMaxDcCurrent(18.9);
        $inverter->setMaker($maker);
        $inverterManager->save($inverter);

        /** @var StringBoxManager $stringBoxManager */
        $stringBoxManager = $this->manager('string_box');

        /** @var StringBox $stringBox */
        $stringBox = $stringBoxManager->create();
        $stringBox->setDescription('desc 1');
        $stringBox->setCode(123);
        $stringBox->setMaker($maker2);
        $stringBox->setInputs(2);
        $stringBox->setOutputs(3);
        $stringBox->setGeneratorLevels(["partner","promotional","finame"]);
        $stringBoxManager->save($stringBox);

        $project1->setDefaults([
            'level' => 'partner',
            'fdi_min' => 0.75,
            'fdi_max' => 1.3,
            'power' => 18,
            'voltage' => 220,
            'phases' => 1,
            'inverter_maker' => $maker->getId(),
            'string_box_maker' => $maker2->getId(),
            'latitude' => 0,
            'longitude' => 0,
            'consumption' => 0
        ]);

        $project2->setDefaults([
            'level' => 'partner',
            'fdi_min' => 0.75,
            'fdi_max' => 1.3,
            'power' => 18,
            'voltage' => 220,
            'phases' => 1,
            'inverter_maker' => $maker->getId(),
            'string_box_maker' => $maker2->getId(),
            'latitude' => 0,
            'longitude' => 0,
            'consumption' => 0
        ]);

        $project3->setDefaults([
            'level' => 'partner',
            'fdi_min' => 0.75,
            'fdi_max' => 1.3,
            'power' => 18,
            'voltage' => 220,
            'phases' => 1,
            'inverter_maker' => $maker->getId(),
            'string_box_maker' => $maker2->getId(),
            'latitude' => 0,
            'longitude' => 0,
            'consumption' => 0
        ]);

        $manager->save($project1);
        $manager->save($project2);
        $manager->save($project3);

        /** @var Bridge $bridge */
        $bridge = $this->getContainer()->get('generator_bridge');

        $this->createStructures();
        // TODO: retorno do "Core::process($parameters)" do metodo resolve
        $result1 = $bridge->resolve($project1);
        $result2 = $bridge->resolve($project2);
        $result3 = $bridge->resolve($project3);

        $structures1 = [];
        $structures2 = [];
        $structures3 = [];

        /** @var ProjectStructure $projectStructure */
        foreach ($result1->getProjectStructures() as $projectStructure) {
            $structureData['id'] = $projectStructure->getStructure()->getId();
            $structureData['quantity'] = $projectStructure->getQuantity();

            $structures1[] = $structureData;
        }

        /** @var ProjectStructure $projectStructure */
        foreach ($result2->getProjectStructures() as $projectStructure) {
            $structureData['id'] = $projectStructure->getStructure()->getId();
            $structureData['quantity'] = $projectStructure->getQuantity();

            $structures2[] = $structureData;
        }

        /** @var ProjectStructure $projectStructure */
        foreach ($result3->getProjectStructures() as $projectStructure) {
            $structureData['id'] = $projectStructure->getStructure()->getId();
            $structureData['quantity'] = $projectStructure->getQuantity();

            $structures3[] = $structureData;
        }

        for ($i = 0; $i < count($structures1); $i++) {
            $this->assertEquals($structures1[$i]['id'], $structures2[$i]['id']);
            $this->assertNotEquals($structures1[$i]['quantity'], $structures2[$i]['quantity']);

            $this->assertEquals($structures2[$i]['id'], $structures3[$i]['id']);
            $this->assertEquals($structures2[$i]['quantity'], $structures3[$i]['quantity']);
        }
        //dump($result2->getProjectStructures()->toArray());

//        self::assertEquals(4, count($result));
//        self::assertEquals(2, $result['inverters'][0]['id']);
    }

    private function createStructures()
    {
        $structureManager = $this->manager('structure');

        $makerManager = $this->manager('maker');

        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Fab. 1');
        $maker->setEnabled(1);
        $maker->setContext('structure');
        $makerManager->save($maker);

        /** @var Maker $maker2 */
        $maker2 = $makerManager->create();
        $maker2->setName('Fab. 2');
        $maker2->setEnabled(1);
        $maker2->setContext('structure');
        $makerManager->save($maker2);

        /** @var Structure $structure */
        $structure = $structureManager->create();
        $structure->setType('ground_portico');
        $structure->setGeneratorLevels(["black","platinum"]);
        $structure->setDescription('Teste');
        $structure->setCode(111);
        $structure->setMaker($maker);
        $structureManager->save($structure);

        /** @var Structure $structure2 */
        $structure2 = $structureManager->create();
        $structure2->setType('ground_clamps');
        $structure2->setGeneratorLevels(["black"]);
        $structure2->setDescription('Teste');
        $structure2->setCode(222);
        $structure2->setMaker($maker);
        $structureManager->save($structure2);

        /** @var Structure $structure10 */
        $structure10 = $structureManager->create();
        $structure10->setType('ground_screw');
        $structure10->setGeneratorLevels(["black"]);
        $structure10->setDescription('Teste');
        $structure10->setCode(1010);
        $structure10->setMaker($maker);
        $structureManager->save($structure10);

        /** @var Structure $structure2 */
        $structure11 = $structureManager->create();
        $structure11->setType('ground_diagonal_union');
        $structure11->setGeneratorLevels(["black"]);
        $structure11->setDescription('Teste');
        $structure11->setCode(1111);
        $structure11->setMaker($maker);
        $structureManager->save($structure11);

        /** @var Structure $structure3 */
        $structure3 = $structureManager->create();
        $structure3->setType('ground_diagonal');
        $structure3->setSize(2);
        $structure3->setGeneratorLevels(["black","platinum"]);
        $structure3->setDescription('Teste');
        $structure3->setCode(333);
        $structure3->setMaker($maker2);
        $structureManager->save($structure3);

        /** @var Structure $structure4 */
        $structure4 = $structureManager->create();
        $structure4->setType('ground_diagonal');
        $structure4->setSize(3);
        $structure4->setGeneratorLevels(["black","platinum"]);
        $structure4->setDescription('Teste');
        $structure4->setCode(444);
        $structure4->setMaker($maker2);
        $structureManager->save($structure4);

        /** @var Structure $structure5 */
        $structure5 = $structureManager->create();
        $structure5->setType('ground_diagonal');
        $structure5->setSize(4);
        $structure5->setGeneratorLevels(["black","platinum"]);
        $structure5->setDescription('Teste');
        $structure5->setCode(555);
        $structure5->setMaker($maker2);
        $structureManager->save($structure5);

        /** @var Structure $structure6 */
        $structure6 = $structureManager->create();
        $structure6->setType('ground_cross');
        $structure6->setSize(3);
        $structure6->setGeneratorLevels(["black"]);
        $structure6->setDescription('Teste');
        $structure6->setCode(666);
        $structure6->setMaker($maker2);
        $structureManager->save($structure6);

        /** @var Structure $structure7 */
        $structure7 = $structureManager->create();
        $structure7->setType('ground_cross');
        $structure7->setSize(4);
        $structure7->setGeneratorLevels(["black"]);
        $structure7->setDescription('Teste');
        $structure7->setCode(777);
        $structure7->setMaker($maker2);
        $structureManager->save($structure7);

        /** @var Structure $structure8 */
        $structure8 = $structureManager->create();
        $structure8->setType('ground_cross');
        $structure8->setSize(5);
        $structure8->setGeneratorLevels(["black"]);
        $structure8->setDescription('Teste');
        $structure8->setCode(777);
        $structure8->setMaker($maker2);
        $structureManager->save($structure8);
    }
}
