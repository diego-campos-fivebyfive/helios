<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Kit\CartPool;
use AppBundle\Manager\CartManager;
use AppBundle\Manager\CartPoolManager;
use AppBundle\Manager\CustomerManager;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Kit\Cart;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CartPoolManagerTest
 * @group cart_pool_manager
 */
class CartPoolManagerTest extends WebTestCase
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

        /** @var CartManager $cartManager */
        $cartManager = $this->getContainer()->get('cart_manager');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $account->getId()
        ]);

        // Dados de Mock para teste da entidade CartPool

        $checkout = [
            'firstName' => 'Nome teste',
            'lastName' => 'Sobrenome teste',
            'document' => '02298095216',
            'email' => 'teste1992@gmail.com',
            'phone' => '42984354582',
            'postcode' => '85065140',
            'state' => 'pr'
        ];

        $code = 'JSGH7327783';
        $metadata = [
           'cart' => [
               'id' => $cart->getId(),
               'account' => $cart->getAccount()
           ],
           'checkount' => $checkout
        ];

        $callbacks = [
            'payment_type' => 'débito',
            'customer_id' => 15,
            'order_id' => 12,
            'payment_id' => 2,
            'amount' => 15000,
            'status' => 'PENDING'
        ];

        /** @var CartPoolManager $cartPoolManager */
        $cartPoolManager = $this->getContainer()->get('cart_pool_manager');

        /** @var CartPool $cartPool */
        $cartPool = $cartPoolManager->create();
        $cartPool
            ->setCode($code)
            ->setMetadata($metadata)
            ->setCallbacks($callbacks);

        $cartPoolManager->save($cartPool);

        self::assertEquals($cartPool->getCode(), 'JSGH7327783');
        self::assertNotNull($cartPool->getCode());
        self::assertInstanceOf(CartPool::class, $cartPool);

        // Teste de atualização do cartPool criado anteriormente


        $code2 = 'JSGH7324585';
        $callbacks2 = [
            'payment_type' => 'débito',
            'customer_id' => 15,
            'order_id' => 25,
            'payment_id' => 2,
            'amount' => 35000,
            'status' => 'PENDING'
        ];

        $cartPool->setCode($code2);
        $cartPool->setCallbacks($callbacks2);

        $cartPoolManager->save($cartPool);

        self::assertEquals($cartPool->getCode(), 'JSGH7324585');

        // Teste de exclusão do cartPool de id = 1

        $cartPool3 = $cartPoolManager->find(1);

        $cartPoolManager->delete($cartPool3);
    }
}
