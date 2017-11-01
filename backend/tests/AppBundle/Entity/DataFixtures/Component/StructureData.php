<?php

namespace Tests\AppBundle\Entity\DataFixtures\Component;

use AppBundle\Entity\Component\Structure;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Entity\DataFixtures\DataFixtureHelper;

class StructureData extends AbstractFixture implements OrderedFixtureInterface
{
    use DataFixtureHelper;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $structure = new Structure();

        $this->fillAndSave($structure, self::getData(), $manager, 'structure');
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @return array
     */
    public static function getData()
    {
        return [
            'description' => self::randomString(4),
            'code' => self::randomString(8),
            'type' => self::randomString(10),
            'subtype' => self::randomString(5),
            'size' => self::randomFloat(),
            'datasheet' => self::randomString(10),
            'image' => self::randomString(10),
            'status' => true,
            'available' => true
        ];
    }
}
