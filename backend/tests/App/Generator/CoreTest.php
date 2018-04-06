<?php

namespace Tests\App\Generator;
use App\Generator\Core;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Maker;
use AppBundle\Manager\InverterManager;
use AppBundle\Service\ProjectGenerator\Core\InverterLoader;

/**
 * Class CoreTest
 * @group generator_core
 */
class CoreTest extends GeneratorTest
{
    public function testGeneratorCore()
    {
        $makerManager = $this->getContainer()->get('maker_manager');

        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Novo Maker');
        $maker->setEnabled(1);
        $maker->setContext('inverter');
        $makerManager->save($maker);

        /** @var InverterManager $inverterManager */
        $inverterManager = $this->getContainer()->get('inverter_manager');

        $all = $inverterManager->findAll();

        foreach ($all as $item) {
            $inverterManager->delete($item);
        }

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter->setDescription('desc');
        $inverter->setCode(123);
        $inverter->setGeneratorLevels(["black","platinum","premium","partner","promotional","finame"]);
        $inverter->setPhases(1);
        $inverter->setPhaseVoltage(220);
        $inverter->setCompatibility(2);
        $inverter->setNominalPower(8.5);
        $inverter->setMinPowerSelection(0);
        $inverter->setMaxPowerSelection(18);
        $inverter->setMpptParallel(true);
        $inverter->setMpptNumber(2);
        $inverter->setMpptMin(300);
        $inverter->setInProtection(true);
        $inverter->setMaxDcVoltage(900);
        $inverter->setMpptMaxDcCurrent(18.9);
        $inverter->setMaker($maker);
        $inverterManager->save($inverter);

        /** @var Inverter $inverter2 */
        $inverter2 = $inverterManager->create();
        $inverter2->setCode(321);
        $inverter2->setGeneratorLevels(["black","platinum","premium","partner","promotional","finame"]);
        $inverter2->setAlternative($inverter->getId());
        $inverter2->setPhases(1);
        $inverter2->setPhaseVoltage(220);
        $inverter2->setCompatibility(1);
        $inverter2->setNominalPower(6);
        $inverter2->setMinPowerSelection(3.4);
        $inverter2->setMaxPowerSelection(18);
        $inverter2->setMpptParallel(true);
        $inverter2->setMpptNumber(2);
        $inverter2->setMpptMin(300);
        $inverter2->setInProtection(true);
        $inverter2->setMaxDcVoltage(900);
        $inverter2->setMpptMaxDcCurrent(18.9);
        $inverter2->setMaker($maker);
        $inverterManager->save($inverter2);

        $inverterLoader = new InverterLoader([
            'manager' => $inverterManager,
            'maker' => $maker->getId()
        ]);

        $inverters = $inverterLoader->all();

        $core = new Core();

        $parameters['module'] = [];
        $parameters['inverters'] = $inverters;
        $parameters['string_boxes'] = [];
        $parameters['power'] = 13;
        $parameters['fdi_min'] = 1;
        $parameters['fdi_max'] = 200;
        $parameters['phase_voltage'] = 220;
        $parameters['phase_number'] = 1;

        $result = $core->process($parameters);

        self::assertEquals(2, count($result['inverters']));
        self::assertEquals(1, $result['inverters'][0]['id']);
        self::assertEquals(1, $result['inverters'][1]['id']);

        $parameters['power'] = 12;

        $result = $core->process($parameters);

        self::assertEquals(2, count($result['inverters']));
        self::assertEquals(2, $result['inverters'][0]['id']);
        self::assertEquals(2, $result['inverters'][1]['id']);

        $parameters['power'] = 8.5;

        $result = $core->process($parameters);

        self::assertEquals(1, count($result['inverters']));
        self::assertEquals(1, $result['inverters'][0]['id']);

        $parameters['power'] = 6;

        $result = $core->process($parameters);

        self::assertEquals(1, count($result['inverters']));
        self::assertEquals(2, $result['inverters'][0]['id']);

        //defaults
        $parameters['string_boxes'] = null; // [];
        $parameters['power'] = null; // 0;
        $parameters['fdi_min'] = null; // 0.75;
        $parameters['fdi_max'] = null; // 1.3;
        $parameters['phase_voltage'] = null; // 220;
        $parameters['phase_number'] = null; // 1;

        $result = $core->process($parameters);

        self::assertEquals(1, count($result['inverters']));
        self::assertEquals(2, $result['inverters'][0]['id']);
    }
}
