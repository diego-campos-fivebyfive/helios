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

/**
 * @Route("cart")
 *
 * @Breadcrumb("Carrinho de compras")
 */
class CartController extends AbstractController
{
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

        $cartHasKitManager = $this->manager('cart_has_kit');

        $cartHasKits = $cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        return $this->render('cart.view', [
            'cartHasKits' => $cartHasKits
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
        $cartHasKit = $cartHasKitManager->create();

        $quantity = $request->get('quantity');

        $cartHasKit->setKit($kit);
        $cartHasKit->setCart($cart);
        $cartHasKit->setQuantity($quantity);

        try {
            $cartHasKitManager->save($cartHasKit);

            return $this->json([], Response::HTTP_OK);
        } catch (\Exception $exception) {
            $message = 'Este kit já foi adicionado ao carrinho';

            if ($cartHasKit->getKit() === null) {
                $message = 'O kit não está disponível';
            }

            if ($cartHasKit->getQuantity() === null) {
                $message = 'Quantidade indisponível';
            }

            return $this->json([
                'message' => $message
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/{id}/quantity", name="cart_add_kit")
     * @Method("put")
     */
    public function updateKitQuantityAction(Request $request, Kit $kit)
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        $quantity = $request->request->getInt('quantity');

        if ($cart && $kit->getStock() >= $quantity) {

            $cartHasKitManager = $this->manager('cart_has_kit');

            $cartHasKit = $cartHasKitManager->findOneBy([
                'cart' => $cart,
                'kit' => $kit
            ]);

            $cartHasKit->setQuantity($quantity);

            $cartHasKitManager->save($cartHasKit);

            return $this->json([], Response::HTTP_OK);
        }

        return $this->json([
            'message' => 'Quantidade indisponível'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
