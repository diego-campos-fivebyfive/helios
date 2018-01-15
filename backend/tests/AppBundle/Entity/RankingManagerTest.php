<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Misc\Ranking;
use AppBundle\Entity\Misc\RankingInterface;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * @group ranking
 */
class RankingManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testDefault()
    {
        $ranking = $this->createRanking();
        $this->assertInstanceOf(Ranking::class, $ranking);
        $this->assertNotNull($ranking);
    }

    private function createRanking()
    {
        $manager = $this->getContainer()->get('ranking_manager');

        /** @var RankingInterface $ranking */
        $ranking = $manager->create();

        $date = new \DateTime('now');

        $ranking
            ->setTarget('Teste um')
            ->setDescription('Descrição do Teste de entidade Ranking')
            ->setCreatedAt($date)
            ->setAmount(5)
        ;

        $manager->save($ranking);

        return $ranking;
    }

}
