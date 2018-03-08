<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
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
     * Default test association
     */
    public function testAccountAssociation()
    {
        $manager = $this->manager('account');

        $accountA = $manager->create();
        $accountA->setContext(BusinessInterface::CONTEXT_ACCOUNT);

        $accountB = $manager->create();
        $accountB->setContext(BusinessInterface::CONTEXT_ACCOUNT);

        $manager->save($accountA);
        $manager->save($accountB);

        // ADD
        $accountA->addChildAccount($accountB);

        $this->assertTrue($accountB->isChildAccount());
        $this->assertTrue($accountA->isParentAccount());
        $this->assertCount(1, $accountA->getChildAccounts()->toArray());
        $this->assertNotNull($accountB->getParentAccount());

        // REMOVE
        $accountA->removeChildAccount($accountB);
        $this->assertNull($accountB->getParentAccount());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAccountAssociationException()
    {
        $manager = $this->manager('account');

        $accountA = $manager->create();
        $accountA->setContext(BusinessInterface::CONTEXT_ACCOUNT);

        $accountB = $manager->create();
        $accountB->setContext(BusinessInterface::CONTEXT_ACCOUNT);

        $accountC = $manager->create();
        $accountC->setContext(BusinessInterface::CONTEXT_ACCOUNT);

        $manager->save($accountA);
        $manager->save($accountB);
        $manager->save($accountC);

        $accountA->addChildAccount($accountB);
        $this->assertTrue($accountB->isChildAccount());

        $accountB->addChildAccount($accountC);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRoleAgentReference()
    {
        $manager = $this->manager('account');

        $account = $manager->create();

        $account->setContext(Customer::CONTEXT_ACCOUNT); // valid account context

        $member = $this->createMember();

        $member->setContext(Customer::CONTEXT_MEMBER); // invalid user role

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
