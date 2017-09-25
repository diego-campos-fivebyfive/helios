<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Customer;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInterface;
use Tests\AppBundle\AppTestCase;

/**
 * @group account
 */
class AccountManagerTest extends AppTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRoleAgentReference()
    {
        $manager = $this->manager('account');

        $account = $manager->create();

        $account->setContext(Customer::CONTEXT_ACCOUNT); // valid account context

        $member = $this->createMember();

        $member->setContext(Customer::CONTEXT_MEMBER);

        $account->setAgent($member);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidContextAccountReference()
    {
        $manager = $this->manager('account');

        $account = $manager->create();

        $account->setContext(Customer::CONTEXT_COMPANY); // invalid account context

        $member = $this->createMember();

        $member->getUser()->addRole(UserInterface::ROLE_PLATFORM_ADMIN); // valid user role

        $account->setAgent($member);
    }

    public function testSuccessAgentToAccountReference()
    {
        $account  = $this->createAccount();
        $member = $this->createMember();

        $member->setAccount($account);

        $this->assertCount(1, $account->getMembers()->toArray());

        $agent = $this->createMember();
        $agent->getUser()->addRole(User::ROLE_PLATFORM_COMMERCIAL);

        $account->setAgent($agent);

        $this->assertNotNull($account->getAgent());
    }

    private function createMember()
    {
        $manager = $this->manager('customer');

        $this->assertNotNull($manager);

        $member = $manager->create();
        $member->setContext(Customer::CONTEXT_MEMBER);

        $user = $this->createUser();

        $member->setUser($user);

        return $member;
    }

    private function createUser()
    {
        $manager = $this->getContainer()->get('fos_user.user_manager');

        /** @var UserInterface $user */
        $user = $manager->createUser();

        $user->addRole(UserInterface::ROLE_OWNER);

        return $user;
    }

    /**
     * @return mixed|object|Customer
     */
    private function createAccount()
    {
        $manager = $this->manager('account');

        $account = $manager->create();

        $account
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setFirstname('Account')
            ->setEmail('testaccount@testaccount.com')
        ;

        $manager->save($account);

        return $account;
    }
}
