<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Tests\AppBundle\Helpers\ObjectHelperTest;
use AppBundle\Entity\Customer as Account;

class AccountData extends AbstractFixture implements OrderedFixtureInterface
{
    use ObjectHelperTest;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $account = new Account();

        $account
            ->setContext(Account::CONTEXT_ACCOUNT)
            ->setFirstname(self::randomString(15))
            ->setEmail(sprintf('%s@%s.com', self::randomString(5), self::randomString(5)))
        ;

        $manager->persist($account);
        $manager->flush();

        $this->addReference('account', $account);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}