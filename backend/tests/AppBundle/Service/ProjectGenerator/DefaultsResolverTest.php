<?php

namespace Tests\AppBundle\Service\ProjectGenerator;

use Tests\AppBundle\AppTestCase;

/**
 * Class DefaultsResolverTest
 * @group generator_defaults
 */
class DefaultsResolverTest extends AppTestCase
{
    public function testResolveRoofType()
    {
        $resolver = $this->getContainer()->get('generator_defaults');

        $defaults = $resolver->resolve();

        $this->assertArrayHasKey('roof_type', $defaults);
    }
}