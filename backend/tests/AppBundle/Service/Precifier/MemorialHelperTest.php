<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\MemorialManager;
use AppBundle\Service\Precifier\MemorialHelper;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class MemorialLoaderTest
 * @group precifier_memorial_helper
 */
class MemorialHelperTest extends WebTestCase
{
    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testLoad()
    {
        /** @var MemorialHelper $memorialHelper */
        $memorialHelper = $this->getContainer()->get('precifier_memorial_helper');

        /** @var Memorial $memorial */
        $memorial = $memorialHelper->load();

        self::assertNotNull($memorial);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testSyncPublishMemorial()
    {
        /** @var MemorialHelper $memorialHelper */
        $memorialHelper = $this->getContainer()->get('precifier_memorial_helper');

        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->getContainer()->get('precifier_memorial_manager');

        /** @var Memorial $memorial */
        $memorial = $memorialManager->find(1);

        $memorialHelper->syncPublishMemorial($memorial);

        /** @var Memorial $memorial */
        $memorial = $memorialManager->find(1);

        self::assertEquals(Memorial::STATUS_PUBLISHED, $memorial->getStatus());

        $memorials = $memorialManager->findAll();

        foreach ($memorials as $memorial) {
            if ($memorial->getId() !== 1) {
                self::assertNotEquals(Memorial::STATUS_PUBLISHED, $memorial->getStatus());
            }
        }
    }
}
