<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use Tests\AppBundle\Helpers\ObjectHelperTest;

trait DataFixtureHelper
{
    use ObjectHelperTest;

    protected function fillAndSave($entity, array $data, $manager, $reference)
    {
        self::fluentSetters($entity, $data);

        $this->save($manager, $entity, $reference);
    }

    protected function save($manager, $entity, $reference)
    {
        $manager->persist($entity);
        $manager->flush();
        $this->addReference($reference, $entity);
    }
}