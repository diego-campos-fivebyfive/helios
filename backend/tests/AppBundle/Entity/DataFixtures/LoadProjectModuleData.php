<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectModule;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProjectModuleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        /** @var Module $module */
        $module = $this->getReference('module');
        /** @var Project $project */
        $project = $this->getReference('project');

        $projectModule = new ProjectModule();

        $projectModule
            ->setProject($project)
            ->setModule($module)
        ;

        $manager->persist($projectModule);
        $manager->flush();

        $this->addReference('project-module', $projectModule);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 2;
    }
}