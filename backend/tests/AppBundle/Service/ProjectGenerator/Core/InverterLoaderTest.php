<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Maker;
use AppBundle\Service\ProjectGenerator\Core\InverterLoader;
use AppBundle\Service\ProjectGenerator\ProjectGenerator;
use Proxies\__CG__\AppBundle\Entity\Order\Element;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Entity\DataFixtures\Component\ModuleData;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * @group core_inverter_loader
 */
class InverterLoaderTest extends AppTestCase
{
    public function testAll()
    {
        $makerManager = $this->manager('maker');

        /** @var Maker $maker */
        $maker = $makerManager->create();

        $maker->setName('Novo Maker');
        $maker->setEnabled(1);
        $maker->setContext('inverter');
        $makerManager->save($maker);

        $maker2 = $makerManager->create();

        $maker2->setName('Novo Maker');
        $maker2->setEnabled(1);
        $maker2->setContext('inverter');
        $makerManager->save($maker2);

        $inverterManager = $this->manager('inverter');

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter->setDescription('desc');
        $inverter->setCode(123);
        $inverter->setMaker($maker);

        $inverterManager->save($inverter);

        /** @var Inverter $inverter2 */
        $inverter2 = $inverterManager->create();
        $inverter2->setCode(123);
        $inverter2->setGeneratorLevels(["black","platinum","premium","partner","promotional","finame"]);
        $inverter2->setAlternative($inverter->getId());
        $inverter2->setPhases(2);
        $inverter2->setPhaseVoltage(3.5);
        $inverter2->setCompatibility(4);
        $inverter2->setNominalPower(5.5);
        $inverter2->setMinPowerSelection(7.7);
        $inverter2->setMaxPowerSelection(8.8);
        $inverter2->setMpptParallel(true);
        $inverter2->setMpptNumber(10);
        $inverter2->setMpptMin(11);
        $inverter2->setInProtection(true);
        $inverter2->setMaxDcVoltage(13.5);
        $inverter2->setMpptMaxDcCurrent(14.5);


        $inverter2->setMaker($maker);
        $inverterManager->save($inverter2);

        $inverterLoader = InverterLoader::create([
            'manager' => $inverterManager,
            'maker' => $maker->getId()
        ]);

        $all = $inverterLoader->all();

        self::assertEquals(2, count($all));

        $inverterLoader2 = InverterLoader::create([
            'manager' => $inverterManager,
            'maker' => $maker2->getId()
        ]);

        $all2 = $inverterLoader2->all();

        self::assertEquals(0, count($all2));
    }

    public function testAlternatives()
    {

        $makerManager = $this->manager('maker');

        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Fab. 1');
        $maker->setEnabled(1);
        $maker->setContext('inverter');
        $makerManager->save($maker);


        $inverterManager = $this->manager('inverter');

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter->setDescription('desc');
        $inverter->setCode(123);
        $inverter->setMaker($maker);
        $inverterManager->save($inverter);

        /** @var Inverter $inverter2 */
        $inverter2 = $inverterManager->create();
        $inverter2->setDescription('desc2');
        $inverter2->setCode(321);
        $inverter2->setMaker($maker);
        $inverterManager->save($inverter2);

        /** @var Maker $maker2 */
        $maker2 = $makerManager->create();
        $maker2->setName('Fab. 2');
        $maker2->setEnabled(1);
        $maker2->setContext('inverter');
        $makerManager->save($maker2);

        /** @var Inverter $inverter3 */
        $inverter3 = $inverterManager->create();
        $inverter3->setDescription('desc2');
        $inverter3->setCode(321);
        $inverter3->setMaker($maker2);
        $inverter3->setAlternative($inverter->getId());
        $inverterManager->save($inverter3);

        $inverterAlt = $inverterManager->create();
        $inverterAlt->setDescription('alt');
        $inverterAlt->setCode(456);
        $inverterAlt->setMaker($maker2);
        $inverterManager->save($inverterAlt);

        $inverterAlt2 = $inverterManager->create();
        $inverterAlt2->setDescription('alt2');
        $inverterAlt2->setCode(654);
        $inverterAlt2->setMaker($maker2);
        $inverterAlt2->setAlternative($inverterAlt->getId());
        $inverterManager->save($inverterAlt2);

        $inverterAlt3 = $inverterManager->create();
        $inverterAlt3->setDescription('alt2');
        $inverterAlt3->setCode(654);
        $inverterAlt3->setMaker($maker2);
        $inverterAlt3->setAlternative($inverter3->getId());
        $inverterManager->save($inverterAlt3);

        $inverterLoader = InverterLoader::create([
            'manager' => $inverterManager,
            'maker' => $maker->getId()
        ]);

        $alternatives = $inverterLoader->alternatives();

        self::assertEquals(2, count($alternatives));

        self::assertEquals(4, $alternatives[0]['id']);
        self::assertEquals(5, $alternatives[1]['id']);
    }
}
