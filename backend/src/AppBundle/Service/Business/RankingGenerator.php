<?php

namespace AppBundle\Service\Business;

use AppBundle\Entity\Misc\Ranking;
use AppBundle\Entity\Misc\RankingInterface;
use AppBundle\Service\Stock\Identity;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RankingGenerator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $target
     * @param $description
     * @param $amount
     * @return RankingInterface
     */
    public function create($target, $description, $amount)
    {
        $manager = $this->container->get('ranking_manager');

        $target = $this->resolveTarget($target);

        /** @var RankingInterface $ranking */
        $ranking = $manager->create();
        $ranking
            ->setTarget($target)
            ->setDescription($description)
            ->setAmount($amount)
        ;

        $manager->save($ranking);

        return $ranking;
    }

    /**
     * @param $target
     * @return mixed
     */
    public function load($target)
    {
        $manager = $this->container->get('ranking_manager');

        $target = $this->resolveTarget($target);

        $ranking = $manager->findBy([
            'target' => $target
        ]);

        return $ranking;
    }

    /**
     * @param $target
     * @return array|string
     */
    private function resolveTarget($target)
    {
        if (is_object($target)) {
            return $target = Identity::create($target);
        }
    }

    public function total($target)
    {
        $rankings = $this->load($target);

        $amount = null;
        foreach ($rankings as $ranking) {
            $amount += $ranking->getAmount();
        }

        return $amount;
    }
}
