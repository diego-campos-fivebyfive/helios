<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInverter;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProjectInverterData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        /** @var Inverter $inverter */
        $inverter = $this->getReference('inverter');
        /** @var Project $project */
        $project = $this->getReference('project');

        $projectInverter = new ProjectInverter();

        $projectInverter
            ->setProject($project)
            ->setInverter($inverter)
        ;

        $manager->persist($project);
        $manager->flush();

        $this->addReference('project-inverter', $projectInverter);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 2;
    }
}