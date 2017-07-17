<?php

namespace Tests\AppBundle\Entity\DataFixtures\Component;

use AppBundle\Entity\Component\Maker;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Entity\DataFixtures\DataFixtureHelper;

class MakerData extends AbstractFixture implements OrderedFixtureInterface
{
    use DataFixtureHelper;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            'context' => Maker::CONTEXT_ALL,
            'name' => 'This is a Maker',
            'enabled' => true
        ];

        $maker = new Maker();

        $this->fillAndSave($maker, $data, $manager, 'maker');
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 0;
    }
}