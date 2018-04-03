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

        $stringBoxLoader = StringBoxLoader::create([
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

        $stringBox2 = $stringBoxManager->create();
        $stringBox2->setDescription('desc 1');
        $stringBox2->setCode(123);
        $stringBox2->setMaker($maker1);

        $stringBox3 = $stringBoxManager->create();
        $stringBox3->setDescription('desc 1');
        $stringBox3->setCode(123);
        $stringBox3->setMaker($maker1);

        $stringBox4 = $stringBoxManager->create();
        $stringBox4->setDescription('desc 2');
        $stringBox4->setCode(567);
        $stringBox4->setMaker($maker2);

        $stringBoxManager->save($stringBox1);
        $stringBoxManager->save($stringBox2);
        $stringBoxManager->save($stringBox3);
        $stringBoxManager->save($stringBox4);

        $loadedStringBoxes = $stringBoxLoader->all();

        self::assertEquals(count($loadedStringBoxes), 3);

        /** @var StringBox $stringBox */
        foreach ($loadedStringBoxes as $stringBox) {
            self::assertNotEquals($stringBox->getMakerId(), 2);
        }
    }
}
