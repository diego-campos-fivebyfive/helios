<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Kit\Kit;
use AppBundle\Manager\KitManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class KitManagerTest
 * @group kit_manager
 */
class KitManagerTest extends WebTestCase
{
    public function testSave()
    {
        /** @var KitManager $kitManager */
        $kitManager = $this->getContainer()->get('kit_manager');

        /** @var Kit $kit */
        $kit = $kitManager->create();

        $kit->setCode('AA22');
        $kit->setDescription('Teste');
        $kit->setPower(2.03);
        $kit->setPrice(2301.20);
        $kit->setStock(30);
        $kit->setImage('http://www.google.com');
        $kit->setPosition(3);
        $kit->setAvailable(true);
        $kit->addComponent(1,
            [
                'code' => 'BBB111',
                'description' => 'Componente Teste',
                'quantity' => 10,
                'position' => 1
            ]
        );
        $kit->addComponent(1,
            [
                'code' => 'BBB111',
                'description' => 'Componente Teste',
                'quantity' => 10,
                'position' => 1
            ]
        );
        $kit->addComponent(2,
            [
                'code' => 'BBB222',
                'description' => 'Componente Teste 2',
                'quantity' => 10,
                'position' => 1
            ]
        );
        $kit->removeComponent(2);

        $kitManager->save($kit);

        self::assertTrue($kit instanceof Kit);
        self::assertEquals($kit->getCode(), 'AA22');
        self::assertEquals($kit->getDescription(), 'Teste');
        self::assertEquals($kit->getPower(), 2.03);
        self::assertEquals($kit->getPrice(), 2301.20);
        self::assertEquals($kit->getStock(), 30);
        self::assertEquals($kit->getImage(), 'http://www.google.com');
        self::assertEquals($kit->getPosition(), 3);
        self::assertTrue($kit->isAvailable());
        self::assertEquals(1, count($kit->getComponents()));
    }
}
