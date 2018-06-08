<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Cart;
use AppBundle\Manager\CartManager;
use AppBundle\Service\Cart\CartPoolHelper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("purchase")
 *
 * @Breadcrumb("Compra")
 * @Security("has_role('ROLE_OWNER')")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/create_cart_pool", name="cart_pool_create")
     * @Method("post")
     */
    public function createCartPoolAction(Request $request)
    {
        $code = $request->get('code');

        $account = $this->account();

        /** @var Cart $cart */
        $cart = $this->getCart();

        $cartHasKitManager = $this->manager('cart_has_kit');

        $cartHasKits = $cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->container->get('cart_pool_helper');

        $items = $cartPoolHelper->formatItems($cartHasKits);

        $checkout = $cart->getCheckout();

        $cartPool = $cartPoolHelper->createCartPool($code, $account, $items, $checkout);

        if ($cartPool) {
            $this->clearCart($cart);
        }

        return $this->json();
    }

    /**
     * Function to clean the cart
     * @param Cart $cart
     */
    private function clearCart(Cart $cart)
    {
        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->container->get('cart_pool_helper');

        $cartPoolHelper->clearCart($cart);
    }

    /**
     * @return null|Cart
     */
    private function getCart()
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        return $cartManager->findOneBy([
            'account' => $this->account()
        ]);
    }
}
