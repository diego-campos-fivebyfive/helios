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
        $inverterManager = $this->manager('inverter');

        $makers = $makerManager->findAll();
        $inverters = $inverterManager->findAll();

        foreach ($makers as $maker1) {
            $makerManager->delete($maker1);
        }

        foreach ($inverters as $inverter1) {
            $inverterManager->delete($inverter1);
        }

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
        $inverter2->setPhaseVoltage(3);
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

        /** @var Inverter $inverter3 */
        $inverter3 = $inverterManager->create();
        $inverter3->setPhases(333);
        $inverter3->setCode(321);
        $inverter3->setMaker($maker2);
        $inverterManager->save($inverter3);

        $inverterLoader = new InverterLoader([
            'manager' => $inverterManager,
            'maker' => $maker->getId()
        ]);

        $all = $inverterLoader->all();

        self::assertEquals(2, count($all));
        self::assertEquals(2, $all[1]['phase_number']);
        self::assertEquals(3, $all[1]['phase_voltage']);
        self::assertEquals(4, $all[1]['compatibility']);
        self::assertEquals(5.5, $all[1]['nominal_power']);
        self::assertEquals(7.7, $all[1]['min_power_selection']);
        self::assertEquals(8.8, $all[1]['max_power_selection']);
        self::assertEquals(true, $all[1]['mppt_parallel']);
        self::assertEquals(10, $all[1]['mppt_number']);
        self::assertEquals(11, $all[1]['mppt_min']);
        self::assertEquals(true, $all[1]['in_protection']);
        self::assertEquals(13.5, $all[1]['max_dc_voltage']);
        self::assertEquals(14.5, $all[1]['mppt_max_dc_current']);

        $inverterLoader2 = new InverterLoader([
            'manager' => $inverterManager,
            'maker' => $maker2->getId()
        ]);

        $all2 = $inverterLoader2->all();

        self::assertEquals(1, count($all2));
    }

    public function testAlternatives()
    {
        $inverterManager = $this->manager('inverter');

        $makerManager = $this->manager('maker');

        $makers = $makerManager->findAll();
        $inverters = $inverterManager->findAll();

        foreach ($makers as $maker1) {
            $makerManager->delete($maker1);
        }

        foreach ($inverters as $inverter1) {
            $inverterManager->delete($inverter1);
        }

        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Fab. 1');
        $maker->setEnabled(1);
        $maker->setContext('inverter');
        $makerManager->save($maker);

        /** @var Maker $maker2 */
        $maker2 = $makerManager->create();
        $maker2->setName('Fab. 2');
        $maker2->setEnabled(1);
        $maker2->setContext('inverter');
        $makerManager->save($maker2);

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter->setPhases(111);
        $inverter->setGeneratorLevels(["black","platinum"]);
        $inverter->setCode(111);
        $inverter->setMaker($maker);
        $inverterManager->save($inverter);

        /** @var Inverter $inverter2 */
        $inverter2 = $inverterManager->create();
        $inverter2->setPhases(222);
        $inverter2->setGeneratorLevels(["black"]);
        $inverter2->setCode(222);
        $inverter2->setMaker($maker);
        $inverterManager->save($inverter2);

        /** @var Inverter $inverter3 */
        $inverter3 = $inverterManager->create();
        $inverter3->setPhases(333);
        $inverter3->setGeneratorLevels(["black","platinum"]);
        $inverter3->setCode(333);
        $inverter3->setMaker($maker2);
        $inverterManager->save($inverter3);

        /** @var Inverter $inverter4 */
        $inverter4 = $inverterManager->create();
        $inverter4->setPhases(444);
        $inverter4->setGeneratorLevels(["black"]);
        $inverter4->setCode(444);
        $inverter4->setMaker($maker2);
        $inverterManager->save($inverter4);

        $inverter2->setAlternative($inverter3->getId());
        $inverterManager->save($inverter2);

        $inverter3->setAlternative($inverter4->getId());
        $inverterManager->save($inverter3);

        $inverter4->setAlternative($inverter2->getId());
        $inverterManager->save($inverter4);

        $inverterLoader = new InverterLoader([
            'manager' => $inverterManager,
            'maker' => $maker->getId()
        ]);

        $alternatives = $inverterLoader->alternatives();

        self::assertEquals(2, count($alternatives));
        self::assertEquals(3, $alternatives[0]['id']);
        self::assertEquals(4, $alternatives[1]['id']);

        //$filter = $inverterLoader->filter('platinum');

        //self::assertEquals(2, count($filter));
        //self::assertEquals(2, $filter[0]['id']);
        //self::assertEquals(4, $filter[1]['id']);
    }

    public function testFindByIds()
    {
        $inverterManager = $this->manager('inverter');

        $makerManager = $this->manager('maker');

        $makers = $makerManager->findAll();
        $inverters = $inverterManager->findAll();

        foreach ($makers as $maker1) {
            $makerManager->delete($maker1);
        }

        foreach ($inverters as $inverter1) {
            $inverterManager->delete($inverter1);
        }

        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Fab. 1');
        $maker->setEnabled(1);
        $maker->setContext('inverter');
        $makerManager->save($maker);

        /** @var Maker $maker2 */
        $maker2 = $makerManager->create();
        $maker2->setName('Fab. 2');
        $maker2->setEnabled(1);
        $maker2->setContext('inverter');
        $makerManager->save($maker2);

        /** @var Inverter $inverter */
        $inverter = $inverterManager->create();
        $inverter->setPhases(111);
        $inverter->setGeneratorLevels(["black","platinum"]);
        $inverter->setCode(111);
        $inverter->setMaker($maker);
        $inverterManager->save($inverter);

        /** @var Inverter $inverter2 */
        $inverter2 = $inverterManager->create();
        $inverter2->setPhases(222);
        $inverter2->setGeneratorLevels(["black"]);
        $inverter2->setCode(222);
        $inverter2->setMaker($maker);
        $inverterManager->save($inverter2);

        /** @var Inverter $inverter3 */
        $inverter3 = $inverterManager->create();
        $inverter3->setPhases(333);
        $inverter3->setGeneratorLevels(["black","platinum"]);
        $inverter3->setCode(333);
        $inverter3->setMaker($maker2);
        $inverterManager->save($inverter3);

        /** @var Inverter $inverter4 */
        $inverter4 = $inverterManager->create();
        $inverter4->setPhases(444);
        $inverter4->setGeneratorLevels(["black"]);
        $inverter4->setCode(444);
        $inverter4->setMaker($maker2);
        $inverterManager->save($inverter4);

        $inverter2->setAlternative($inverter3->getId());
        $inverterManager->save($inverter2);

        $inverter3->setAlternative($inverter4->getId());
        $inverterManager->save($inverter3);

        $inverter4->setAlternative($inverter2->getId());
        $inverterManager->save($inverter4);

        $inverterLoader = new InverterLoader([
            'manager' => $inverterManager,
            'maker' => $maker->getId()
        ]);

        $ids = [1, 2];

        $results = $inverterLoader->findByIds($ids);

        self::assertEquals(count($ids), count($results));

        foreach ($results as $result) {
            self::assertTrue(in_array($result->getId(), $ids));
        }
    }
}
