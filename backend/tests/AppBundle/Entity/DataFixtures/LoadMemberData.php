<?php

namespace Tests\AppBundle\Entity\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Customer as Member;
use Tests\AppBundle\Helpers\ObjectHelperTest;

class LoadMemberData extends AbstractFixture implements OrderedFixtureInterface
{
    use ObjectHelperTest;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $member = new Member();

        $member
            ->setContext(Member::CONTEXT_MEMBER)
            ->setFirstname(self::randomString(15))
            ->setEmail(sprintf('%s@%s.com', self::randomString(5), self::randomString(5)))
            ->setAccount($this->getReference('account'))
        ;

        $manager->persist($member);
        $manager->flush();

        $this->addReference('member', $member);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 1;
    }
}
