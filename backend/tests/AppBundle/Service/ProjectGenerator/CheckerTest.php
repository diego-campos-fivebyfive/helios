<?php

namespace Tests\AppBundle\Service\ProjectGenerator;

use AppBundle\Service\ProjectGenerator\ProjectGenerator;
use Tests\AppBundle\AppTestCase;

/**
 * Class GeneratorCheckerTest
 * @group generator_checker
 */
class GeneratorCheckerTest extends AppTestCase
{
    public function testWithDefaults()
    {
        $defaults = $this->getDefaults();

        $checker = $this->getContainer()->get('generator_checker');

        $this->assertFalse($checker->hasError());
    }

    /**
     * @param array $defaults
     * @return array
     */
    private function getDefaults(array $defaults = [])
    {
        return ProjectGenerator::getDefaults($defaults);
    }
}