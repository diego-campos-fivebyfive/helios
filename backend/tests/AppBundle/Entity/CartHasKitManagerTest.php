<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Kit\CartHasKit;
use AppBundle\Entity\Kit\Kit;
use AppBundle\Manager\CartHasKitManager;
use AppBundle\Manager\CartManager;
use AppBundle\Manager\KitManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class CartManagerTest
 * @group cart_has_kit_manager
 */
class CartHasKitManagerTest extends WebTestCase
{
    public function testSave()
    {
        /** @var KitManager $kitManager */
        $kitManager = $this->getContainer()->get('kit_manager');

        /** @var CartManager $cartManager */
        $cartManager = $this->getContainer()->get('cart_manager');

        /** @var Kit $kit */
        $kit = $kitManager->findOneBy([
            'available' => true,
            'id' => 2
        ]);

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'id' => 3
        ]);

        /** @var CartHasKitManager $manager */
        $manager = $this->getContainer()->get('cart_has_kit_manager');

        /** @var CartHasKit $cartHasKit */
        $cartHasKit = $manager->create();

        $cartHasKit->setQuantity(2);
        $cartHasKit->setCart($cart);
        $cartHasKit->setKit($kit);

        $manager->save($cartHasKit);

        self::assertEquals($cartHasKit->getCart(), $cart);
        self::assertEquals($cartHasKit->getKit(), $kit);
        self::assertEquals($cartHasKit->getQuantity(), 2);
    }
}
