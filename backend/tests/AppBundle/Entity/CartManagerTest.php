<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Kit\Cart;
use AppBundle\Manager\CartManager;
use AppBundle\Manager\CustomerManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CartManagerTest
 * @group cart_manager
 */
class CartManagerTest extends WebTestCase
{
    public function testSave()
    {
        /** @var CustomerManager $accountManager */
        $accountManager = $this->getContainer()->get('customer_manager');

        /** @var Customer $account */
        $account = $accountManager->findOneBy([
            'context' => 'account',
            'id' => 19
        ]);

        /** @var CartManager $manager */
        $manager = $this->getContainer()->get('cart_manager');

        /** @var Cart $cart */
        $cart = $manager->create();

        $cart->setAccount($account);

        $manager->save($cart);

        self::assertEquals($cart->getAccount(), $account);
    }
}
