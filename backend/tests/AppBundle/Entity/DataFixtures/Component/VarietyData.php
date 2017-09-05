<?php

namespace Tests\AppBundle\Entity\DataFixtures\Component;

use AppBundle\Entity\Component\Variety;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Entity\DataFixtures\DataFixtureHelper;

class VarietyData extends AbstractFixture implements OrderedFixtureInterface
{
    use DataFixtureHelper;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            'code' => md5(uniqid(time())),
            'description' => 'This is a Variety ' . uniqid(),
            'type' => 'cabo',
            'subtype' => 'conector',
            'promotional' => false,
            'maker' => $this->getReference('maker'),
        ];

        $variety = new Variety();

        $this->fillAndSave($variety, $data, $manager, 'variety');
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}