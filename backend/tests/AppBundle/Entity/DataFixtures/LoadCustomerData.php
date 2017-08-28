<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Customer;
use Tests\AppBundle\Helpers\ObjectHelperTest;

class LoadCustomerData extends AbstractFixture implements OrderedFixtureInterface
{
    use ObjectHelperTest;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $customer = new Customer();

        $customer
            ->setContext(Customer::CONTEXT_COMPANY)
            ->setFirstname(self::randomString(15))
            ->setEmail(sprintf('%s@%s.com', self::randomString(5), self::randomString(5)))
        ;

        $manager->persist($customer);
        $manager->flush();

        $this->addReference('customer', $customer);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}