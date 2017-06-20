<?php

namespace ApiBundle\Tests\Provider;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class AccountProviderTest
 * @group account_provider
 */
class AccountProviderTest extends WebTestCase
{
    public function testDefault()
    {
        $provider = $this->getContainer()->get('account_provider');
        //$accounts = $provider->paginate(5);

        $provider->get();

        dump($provider); die;
    }
}