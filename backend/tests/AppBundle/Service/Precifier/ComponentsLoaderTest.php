<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Service\Precifier\ComponentsLoader;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CalculatorTest
 * @group precifier_components_loader
 */
class ComponentsLoaderTest extends WebTestCase
{
    public function testLoad()
    {
        /** @var ComponentsLoader $componentsLoader */
        $componentsLoader = $this->getContainer()->get('precifier_components_loader');

        $components = $componentsLoader->load();

        self::assertArrayHasKey('module', $components);
        self::assertArrayHasKey('inverter', $components);
        self::assertArrayHasKey('string_box', $components);
        self::assertArrayHasKey('variety', $components);
        self::assertArrayHasKey('structure', $components);
    }
}
