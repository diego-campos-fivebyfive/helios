<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Service\Precifier\MemorialLoader;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class MemorialLoaderTest
 * @group precifier_memorial_loader
 */
class MemorialLoaderTest extends WebTestCase
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testLoad()
    {
        /** @var MemorialLoader $memorialLoader */
        $memorialLoader = $this->getContainer()->get('precifier_memorial_loader');

        /** @var Memorial $memorial */
        $memorial = $memorialLoader->load();

        self::assertNotNull($memorial);
    }
}
