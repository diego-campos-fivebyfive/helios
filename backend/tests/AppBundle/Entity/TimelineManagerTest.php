<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\TimelineInterface;
use Tests\AppBundle\AppTestCase;

/**
 * @group timeline
 */
class TimelineManagerTest extends AppTestCase
{
    public function testCreateTimeline()
    {
        $timeline = $this->createTimeline();

        $this->assertNotNull($timeline->getId());
    }

    private function createTimeline()
    {
        $manager = $this->manager('timeline');

        /** @var TimelineInterface $timeline */
        $timeline = $manager->create();

        $date = new \DateTime('now');
        $teste = 'Teste de inserção de atributo';
        $timeline
            ->setMessage('Message Teste')
            ->setTarget('Teste Target')
            ->addAttribute('Teste', $teste)
            ->setCreatedAt($date);

        $manager->save($timeline);

        return $timeline;
    }
}
