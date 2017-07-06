<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectInverter;
use AppBundle\Entity\Component\ProjectModule;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Helpers\ObjectHelperTest;

class LoadProjectAreaData extends AbstractFixture implements OrderedFixtureInterface
{
    use ObjectHelperTest;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        /** @var ProjectInverter $projectInverter */
        $projectInverter = $this->getReference('project-inverter');
        /** @var ProjectModule $projectModule */
        $projectModule = $this->getReference('project-module');

        $data = [
            'inclination' => self::randomFloat(),
            'orientation' => self::randomFloat(),
            'stringNumber' => self::randomInt(),
            'moduleString' => self::randomInt(),
            'loss' => self::randomFloat()
        ];

        $projectArea = new ProjectArea();

        self::fluentSetters($projectArea, $data);

        $projectArea
            ->setProjectModule($projectModule)
            ->setProjectInverter($projectInverter);

        $manager->persist($projectArea);
        $manager->flush();

        $this->addReference('project-area', $projectArea);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 3;
    }
}