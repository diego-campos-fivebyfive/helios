<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Service\Precifier\ComponentsLoader;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ComponentsLoaderTest
 * @group precifier_components_loader
 */
class ComponentsLoaderTest extends WebTestCase
{
    public function testLoadAll()
    {
        /** @var ComponentsLoader $componentsLoader */
        $componentsLoader = $this->getContainer()->get('precifier_components_loader');

        $components = $componentsLoader->loadAll();

        self::assertArrayHasKey('module', $components);
        self::assertArrayHasKey('inverter', $components);
        self::assertArrayHasKey('string_box', $components);
        self::assertArrayHasKey('variety', $components);
        self::assertArrayHasKey('structure', $components);

        $components = $componentsLoader->loadAll(['module', 'structure']);

        self::assertEquals(2, count($components));
        self::assertArrayHasKey('module', $components);
        self::assertArrayHasKey('structure', $components);
    }

    public function testLoadByIds()
    {
        /** @var ComponentsLoader $componentsLoader */
        $componentsLoader = $this->getContainer()->get('precifier_components_loader');

        $components = $componentsLoader->loadByIds([
            'inverter' => [6418]
        ]);

        self::assertArrayHasKey('inverter', $components);
        self::assertArrayHasKey(6418, $components['inverter']);
    }
}
