<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Kit\CartHasKit;
use AppBundle\Entity\Kit\Kit;
use AppBundle\Manager\CartManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("cart")
 *
 * @Breadcrumb("Carrinho de compras")
 * @Security("has_role('ROLE_OWNER')")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/{id}/items", name="cart_items")
     * @Method("get")
     */
    public function getCartItemsAction(Cart $cart)
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        if (!$cart) {
            $cart = $cartManager->create();

            $cart->setAccount($this->account());

            $cartManager->save($cart);
        }

        $cartHasKitManager = $this->manager('cart_has_kit');

        $cartHasKits = $cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        $total = 0;
        $kits = [];

        /** @var CartHasKit $cartHasKit */
        foreach ($cartHasKits as $cartHasKit) {
            $subTotal = $cartHasKit->getKit()->getPrice() * $cartHasKit->getQuantity();

            $kits[] = [
                'kit' => $cartHasKit->getKit(),
                'quantity' => $cartHasKit->getQuantity(),
                'total' => $subTotal
            ];

            $total += $subTotal;
        }

        return $this->render('cart.items', [
            'cart' => $cart,
            'kits' => $kits,
            'total' => $total,
            'kitsQuantity' => count($cartHasKits)
        ]);
    }

    /**
     * @Route("/show", name="cart_show")
     * @Method("get")
     */
    public function showCartAction()
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        if (!$cart) {
            $cart = $cartManager->create();

            $cart->setAccount($this->account());

            $cartManager->save($cart);
        }

        return $this->render('cart.show', [
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/{id}/add_kit", name="cart_add_kit")
     * @Method("post")
     */
    public function addKitAction(Request $request, Kit $kit)
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        if (!$cart) {
            $cart = $cartManager->create();

            $cart->setAccount($this->account());

            $cartManager->save($cart);
        }

        $cartHasKitManager = $this->manager('cart_has_kit');

        /** @var CartHasKit $cartHasKit */
        $cartHasKit = $cartHasKitManager->findOneBy([
            'cart' => $cart,
            'kit' => $kit
        ]);

        $status = Response::HTTP_OK;
        $message = 'Kit adicionado com sucesso';

        if (!$cartHasKit) {
            $cartHasKit = $cartHasKitManager->create();

            $quantity = $request->get('quantity');

            $cartHasKit->setKit($kit);
            $cartHasKit->setCart($cart);
            $cartHasKit->setQuantity($quantity);

            if (!$cartHasKit->getKit() || !$cartHasKit->getQuantity()) {
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = !$cartHasKit->getKit() ? 'O kit não está disponível' : 'Quantidade indisponível';
            } else {
                $cartHasKitManager->save($cartHasKit);
            }
        } else {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = 'Este kit já foi adicionado ao carrinho';
        }

        return $this->json([
            'message' => $message
        ], $status);
    }
}
