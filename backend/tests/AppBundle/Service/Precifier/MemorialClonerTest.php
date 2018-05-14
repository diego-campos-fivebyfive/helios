<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Entity\Precifier\Range;
use AppBundle\Manager\Precifier\MemorialManager;
use AppBundle\Service\Precifier\MemorialCloner;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class MemorialClonerTest
 * @group precifier_memorial_cloner
 */
class MemorialClonerTest extends WebTestCase
{
    public function testExecute()
    {
        /** @var MemorialCloner $memorialCloner */
        $memorialCloner = $this->getContainer()->get('precifier_memorial_cloner');

        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->getContainer()->get('precifier_memorial_manager');

        /** @var Memorial $memorial */
        $memorial = $memorialManager->find(1);

        $clone = $memorialCloner->execute($memorial);

        self::assertNotNull($clone);
    }

    public function testConvertLevel()
    {
        /** @var MemorialCloner $memorialCloner */
        $memorialCloner = $this->getContainer()->get('precifier_memorial_cloner');

        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->getContainer()->get('precifier_memorial_manager');

        /** @var Memorial $memorial */
        $memorial = $memorialManager->find(3);

        $source = 'platinum';
        $target = 'titanium';

        $memorialCloner->convertLevel($memorial, $source, $target);

        /** @var Range $range */
        foreach ($memorial->getRanges() as $range) {
            self::assertEquals($range->getMetadata()[$source], $range->getMetadata()[$target]);
        }
    }
}
