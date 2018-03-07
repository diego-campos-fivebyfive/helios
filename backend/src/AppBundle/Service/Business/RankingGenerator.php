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
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $manager;

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

        $targetMutate = $this->resolveTarget($target);

        /** @var RankingInterface $ranking */
        $ranking = $manager->create();
        $ranking
            ->setTarget($targetMutate)
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
            $target = Identity::create($target);
        }

        return $target;
    }

    private function total($target)
    {
        $rankings = $this->load($target);

        $amount = null;
        foreach ($rankings as $ranking) {
            $amount += $ranking->getAmount();
        }

        return $amount;
    }

    /**
     * @param $target
     */
    public function refreshRanking($target)
    {
        $ranking = $this->total($target);

        $entity = $this->reverseTarget($target);

        if(method_exists($entity, 'setRanking')){

            $entity->setRanking($ranking);

            $manager = $this->getManager();

            $manager->persist($entity);
            $manager->flush();
        }
    }

    /**
     * @param $target
     * @return object
     */
    private function reverseTarget($target)
    {
        if($target instanceof Ranking)
            $target = $target->getTarget();

        if(!is_object($target)){

            list($class, $id) = explode('::', $target);

            /** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
            $doctrine = $this->container->get('doctrine');

            $manager = $doctrine->getManager();

            $target = $manager->find($class, $id);
        }

        return $target;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|\Doctrine\ORM\EntityManagerInterface|object
     */
    private function getManager()
    {
        if(!$this->manager){

            /** @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine */
            $doctrine = $this->container->get('doctrine');

            $this->manager = $doctrine->getManager();
        }

        return $this->manager;
    }
}
