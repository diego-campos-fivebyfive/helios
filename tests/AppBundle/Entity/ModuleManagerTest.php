<?php

namespace Tests\AppBundle\Entity;

use Tests\AppBundle\AppTestCase;

/**
 * Class ModuleManagerTest
 * @group module
 */
class ModuleManagerTest extends AppTestCase
{
    public function testDefault()
    {
        $module = $this->getFixture('module');

        $this->assertNotNull($module->getCreatedAt());
    }
}