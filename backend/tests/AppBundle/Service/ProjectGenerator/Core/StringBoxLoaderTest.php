<?php

namespace Tests\AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\StringBox;
use AppBundle\Manager\MakerManager;
use AppBundle\Manager\StringBoxManager;
use AppBundle\Service\ProjectGenerator\Core\StringBoxLoader;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Entity\DataFixtures\Component\ModuleData;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * @group core_string_box_loader
 */
class StringBoxLoaderTest extends AppTestCase
{
    public function testAll()
    {
        /** @var StringBoxManager $stringBoxManager */
        $stringBoxManager = $this->getContainer()->get('string_box_manager');

        /** @var MakerManager $makerManager */
        $makerManager = $this->getContainer()->get('maker_manager');

        $makers = $makerManager->findAll();
        $stringBoxes = $stringBoxManager->findAll();

        foreach ($makers as $maker1) {
            $makerManager->delete($maker1);
        }

        foreach ($stringBoxes as $stringBox1) {
            $stringBoxManager->delete($stringBox1);
        }

        $stringBoxLoader = new StringBoxLoader([
            'manager' => $stringBoxManager,
            'maker' => 1
        ]);

        /** @var Maker $maker1 */
        $maker1 = $makerManager->create();
        $maker1->setName('Novo Maker');
        $maker1->setEnabled(1);
        $maker1->setContext('string_box');

        $maker2 = $makerManager->create();
        $maker2->setName('Novo Maker');
        $maker2->setEnabled(1);
        $maker2->setContext('string_box');

        $makerManager->save($maker1);
        $makerManager->save($maker2);

        /** @var StringBox $stringBox1 */
        $stringBox1 = $stringBoxManager->create();
        $stringBox1->setDescription('desc 1');
        $stringBox1->setCode(123);
        $stringBox1->setMaker($maker1);
        $stringBox1->setInputs(2);
        $stringBox1->setOutputs(3);

        $stringBox2 = $stringBoxManager->create();
        $stringBox2->setDescription('desc 1');
        $stringBox2->setCode(123);
        $stringBox2->setMaker($maker1);
        $stringBox2->setInputs(1);
        $stringBox2->setOutputs(1);

        $stringBox3 = $stringBoxManager->create();
        $stringBox3->setDescription('desc 1');
        $stringBox3->setCode(123);
        $stringBox3->setMaker($maker1);
        $stringBox3->setInputs(5);
        $stringBox3->setOutputs(5);

        $stringBox4 = $stringBoxManager->create();
        $stringBox4->setDescription('desc 2');
        $stringBox4->setCode(567);
        $stringBox4->setMaker($maker2);
        $stringBox4->setInputs(3);
        $stringBox4->setOutputs(3);

        $stringBoxManager->save($stringBox1);
        $stringBoxManager->save($stringBox2);
        $stringBoxManager->save($stringBox3);
        $stringBoxManager->save($stringBox4);

        $loadedStringBoxes = $stringBoxLoader->all();

        self::assertEquals(count($loadedStringBoxes), 3);

        /** @var StringBox $stringBox */
        foreach ($loadedStringBoxes as $stringBox) {
            self::assertNotEquals($stringBox['id'], 4);
        }

        // Todos os String Boxes
        $stringBoxLoader = new StringBoxLoader([
            'manager' => $stringBoxManager,
            'maker' => null
        ]);

        $allStringBoxes = $stringBoxLoader->all();

        self::assertEquals(count($allStringBoxes), 4);
    }

    public function testAlternatives()
    {
        $stringBoxManager = $this->getContainer()->get('string_box_manager');

        $makerManager = $this->getContainer()->get('maker_manager');

        $makers = $makerManager->findAll();
        $stringBoxes = $stringBoxManager->findAll();

        foreach ($makers as $maker1) {
            $makerManager->delete($maker1);
        }

        foreach ($stringBoxes as $stringBox1) {
            $stringBoxManager->delete($stringBox1);
        }

        /** @var Maker $maker */
        $maker = $makerManager->create();
        $maker->setName('Fab. 1');
        $maker->setEnabled(1);
        $maker->setContext('string_box');
        $makerManager->save($maker);

        /** @var Maker $maker2 */
        $maker2 = $makerManager->create();
        $maker2->setName('Fab. 2');
        $maker2->setEnabled(1);
        $maker2->setContext('string_box');
        $makerManager->save($maker2);

        /** @var StringBox $stringBox */
        $stringBox = $stringBoxManager->create();
        $stringBox->setGeneratorLevels(["black","platinum"]);
        $stringBox->setCode(111);
        $stringBox->setMaker($maker);
        $stringBox->setDescription('desc');
        $stringBoxManager->save($stringBox);

        /** @var StringBox $stringBox2 */
        $stringBox2 = $stringBoxManager->create();
        $stringBox2->setGeneratorLevels(["black"]);
        $stringBox2->setCode(222);
        $stringBox2->setMaker($maker);
        $stringBox2->setDescription('desc');
        $stringBoxManager->save($stringBox2);

        /** @var StringBox $stringBox3 */
        $stringBox3 = $stringBoxManager->create();
        $stringBox3->setGeneratorLevels(["black","platinum"]);
        $stringBox3->setCode(333);
        $stringBox3->setMaker($maker2);
        $stringBox3->setDescription('desc');
        $stringBoxManager->save($stringBox3);

        /** @var StringBox $stringBox4 */
        $stringBox4 = $stringBoxManager->create();
        $stringBox4->setGeneratorLevels(["black"]);
        $stringBox4->setCode(444);
        $stringBox4->setMaker($maker2);
        $stringBox4->setDescription('desc');
        $stringBoxManager->save($stringBox4);

        $stringBox2->setAlternative($stringBox3->getId());
        $stringBoxManager->save($stringBox2);

        $stringBox3->setAlternative($stringBox4->getId());
        $stringBoxManager->save($stringBox3);

        $stringBox4->setAlternative($stringBox2->getId());
        $stringBoxManager->save($stringBox4);

        $stringBoxLoader = new StringBoxLoader([
            'manager' => $stringBoxManager,
            'maker' => $maker->getId()
        ]);

        $alternatives = $stringBoxLoader->alternatives();

        self::assertEquals(2, count($alternatives));
        self::assertEquals(3, $alternatives[0]['id']);
        self::assertEquals(4, $alternatives[1]['id']);

        //$filter = $stringBoxLoader->filter('platinum');

        //self::assertEquals(1, count($filter));
        //self::assertEquals(1, $filter[0]['id']);
    }
}
