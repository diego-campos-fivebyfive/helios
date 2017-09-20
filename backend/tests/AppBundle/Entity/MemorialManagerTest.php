<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Pricing\Memorial;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class MemorialManagerTest
 * @group memorial
 */
class MemorialManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    /**
     * @var \AppBundle\Manager\Pricing\MemorialManager
     */
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $this->manager = $this->manager('memorial');
    }

    public function testStatusManipulation()
    {
        /** @var \AppBundle\Entity\Pricing\MemorialInterface $memorial */
        $memorial = $this->manager->create();

        $this->assertTrue($memorial->isPending());
        $this->assertFalse($memorial->isEnabled());
        $this->assertFalse($memorial->isExpired());

        $memorial->setStatus(Memorial::STATUS_ENABLED);

        $this->assertTrue($memorial->isEnabled());
        $this->assertNotNull($memorial->getPublishedAt());
    }

    public function testFluentSetters()
    {
        $data = [
            'name' => 'This is a memorial',
            'startAt' => new \DateTime('1 month ago'),
            'publishedAt' => new \DateTime('25 days ago'),
            'expiredAt' => new \DateTime(),
            'levels' => ['platinum', 'partner', 'premium']
        ];

        $memorial = $this->manager->create();

        self::fluentSettersTest($memorial, $data);
    }
}
