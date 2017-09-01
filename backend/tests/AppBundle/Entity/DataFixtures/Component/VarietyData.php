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
<<<<<<< HEAD
            'promotional' => false,
            'maker' => $this->getReference('maker')
=======
            'maker' => $this->getReference('maker'),
            'promotional' => false
>>>>>>> cf1dbd57bae7f1f089ca9b03e846a7af1a7d81b1
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