<?php

namespace Tests\AppBundle\Entity\DataFixtures\Component;

use AppBundle\Entity\Component\StringBox;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Entity\DataFixtures\DataFixtureHelper;

class StringBoxData extends AbstractFixture implements OrderedFixtureInterface
{
    use DataFixtureHelper;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            'code' => '1SLM300101A0790',
            'description' => 'STRING BOX ABB 1 OU 2 CORDA 1 SAIDA - no fusiveis',
            'inputs' => 3,
            'outputs' => 2,
            'fuses' => 1,
            'promotional' => false,
            'maker' => $this->getReference('maker')
        ];

        $stringBox = new StringBox();

        $this->fillAndSave($stringBox, $data, $manager, 'string_box');
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}