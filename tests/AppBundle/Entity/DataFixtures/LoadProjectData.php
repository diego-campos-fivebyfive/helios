<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use AppBundle\Entity\Component\Project;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Helpers\ObjectHelperTest;

class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    use ObjectHelperTest;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $member = $this->getReference('member');
        $customer = $this->getReference('customer');

        $data = [
            'roofType' => 'TYPE_OF_ROOF',
            'infConsumption' => 15000,
            'infPower' => 240,
            'structureType' => Project::STRUCTURE_SICES,
            'customer' => $customer,
            'member' => $member,
            'number' => self::randomInt(),
            'identifier' => self::randomString(5),
            'invoiceBasePrice' => self::randomFloat(),
            'deliveryBasePrice' => self::randomFloat(),
            'invoicePriceStrategy' => 1,
            'deliveryPriceStrategy' => 1,
            'address' => self::randomString(25),
            'latitude' => self::randomFloat(),
            'longitude' => self::randomFloat()
        ];

        $project = new Project();

        self::fluentSetters($project, $data);

        $manager->persist($project);
        $manager->flush();

        $this->setReference('project', $project);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 2;
    }
}