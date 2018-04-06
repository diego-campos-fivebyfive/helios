<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\ProjectModule;
use AppBundle\Entity\Component\StringBox;
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
            ->setQuantity(1)
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

        // TODO: retorno do "Core::process($parameters)" do metodo resolve
        $result = $bridge->resolve($project);

        self::assertEquals(4, count($result));
        self::assertEquals(2, $result['inverters'][0]['id']);
    }
}
