<?php

namespace Tests\AppBundle\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class AccountManagerTest extends WebTestCase
{
    public function testFirstTesting()
    {
        $manager = $this->getContainer()->get('account_manager');

        $account = $manager->create();

        $this->assertNull($account->getId());
    }
}