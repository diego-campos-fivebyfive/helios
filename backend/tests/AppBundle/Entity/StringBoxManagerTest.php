<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\StringBox;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * @group string_box
 */
class StringBoxManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDefault()
    {
        $stringBox = $this->getFixture('string_box');
        $this->assertInstanceOf(StringBox::class, $stringBox);
    }
}