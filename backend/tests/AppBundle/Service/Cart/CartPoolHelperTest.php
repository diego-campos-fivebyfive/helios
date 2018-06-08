<?php

namespace Tests\AppBundle\Service\Cart;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Manager\AccountManager;
use AppBundle\Manager\CartHasKitManager;
use AppBundle\Manager\CartManager;
use AppBundle\Entity\Kit\Cart;
use AppBundle\Service\Cart\CartPoolHelper;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CartPoolHelperTest
 * @group cart_pool_helper
 */
class CartPoolHelperTest extends WebTestCase
{
    public function testCreate()
    {
        $code = md5(uniqid());

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->getContainer()->get('cart_pool_helper');


        /** @var AccountManager $accountManager */
        $accountManager = $this->getContainer()->get('account_manager');

        /** @var AccountInterface $account */
        $account = $accountManager->find(2209);

        $cartPool = $cartPoolHelper->createCartPool($code, $account);

        self::assertTrue($cartPool instanceof CartPool);

        $code = md5(uniqid());

        /** @var AccountManager $accountManager */
        $accountManager = $this->getContainer()->get('account_manager');

        /** @var AccountInterface $account */
        $account = $accountManager->find(2215);


        $cartPool = $cartPoolHelper->createCartPool($code, $account);

        self::assertNull($cartPool);
    }

    public function testFormatItems()
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->getContainer()->get('cart_manager');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => 2209
        ]);

        /** @var CartHasKitManager $cartHasKitManager */
        $cartHasKitManager = $this->getContainer()->get('cart_has_kit_manager');

        // Mock Data
        $items = $cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        $keys = [
            'name',
            'description',
            'value',
            'quantity',
            'sku'
        ];

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->getContainer()->get('cart_pool_helper');

        $formatedItems = $cartPoolHelper->formatItems($items);

        foreach ($formatedItems as $formatedItem) {
            self::assertEmpty(array_diff(array_keys($formatedItem), $keys));
        }
    }

    public function testFormatCheckout()
    {
        $checkout = [
            "firstName" => 'Gianluca',
            "lastName" => 'Bine',
            "documentType" => 'CPF',
            "document" => '088.463.559-70',
            "email" => 'gian_bine@hotmail.com',
            "phone" => '(42) 3623-8320',
            "street" => 'Rua Teste',
            "number" => '123',
            "complement" => '',
            "neighborhood" => 'Teste',
            "city" => 'Teste',
            "state" => 'PR',
            "postcode" => '85015-310',
            "country" => "Brasil",
            "shippingName" => 'Gianluca Bine',
            "shippingStreet" => 'Rua Teste',
            "shippingComplement" => '',
            "shippingNumber" => 123,
            "shippingNeighborhood" => 'Teste',
            "shippingCity" => 'Teste',
            "shippingState" => 'PR',
            "shippingPostcode" => '85015-310',
            "differentDelivery" => true
        ];

        $keys = [
            "firstName",
            "lastName",
            "documentType",
            "documentNumber",
            "email",
            "phone",
            "street",
            "number",
            "complement",
            "neighborhood",
            "city",
            "state",
            "zipcode",
            "country",
            "shipping",
            "differentDelivery"
        ];

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->getContainer()->get('cart_pool_helper');

        $formatedCheckout = $cartPoolHelper->formatCheckout($checkout);

        self::assertEmpty(array_diff(array_keys($formatedCheckout), $keys));
    }
}
