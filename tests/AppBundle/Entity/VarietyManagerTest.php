<?php

namespace Tests\AppBundle\Entity;

use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class VarietyManagerTest
 * @group variety
 */
class VarietyManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDefault()
    {
       $variety = $this->getFixture('variety');

        $this->assertNotNull($variety->getId());
    }
}