<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Kit\CartHasKit;
use AppBundle\Entity\Kit\Kit;
use AppBundle\Form\Cart\CheckoutType;
use AppBundle\Manager\CartManager;
use AppBundle\Service\Cart\CartPoolHelper;
use AppBundle\Service\Checkout\Getnet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("cart")
 *
 * @Breadcrumb("Carrinho de compras")
 * @Security("has_role('ROLE_OWNER')")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/checkout", name="cart_checkout")
     */
    public function getCheckoutAction(Request $request)
    {
        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Getnet $getNet */
            $getNet = new GetNet(GetNet::HOMOLOG);

            $accessToken = $getNet->getAccessToken();

            $keys = [
                "firstName",
                "lastName",
                "documentType",
                "document",
                "email",
                "phone",
                "postcode",
                "state",
                "city",
                "neighborhood",
                "street",
                "number",
                "complement",
                "differentDelivery",
                "shippingName",
                "shippingEmail",
                "shippingPhone",
                "shippingPostcode",
                "shippingState",
                "shippingCity",
                "shippingNeighborhood",
                "shippingStreet",
                "shippingNumber",
                "shippingComplement",
            ];

            /** @var CartManager $cartManager */
            $cartManager = $this->manager('cart');

            /** @var Cart $cart */
            $cart = $cartManager->findOneBy([
                'account' => $this->account()
            ]);

            $cartHasKitManager = $this->manager('cart_has_kit');

            $items = $cartHasKitManager->findBy([
                'cart' => $cart
            ]);

            $data = [];

            foreach ($keys as $key) {
                $data[$key] = $form->get($key)->getData();
            }

            /** @var CartPoolHelper $cartPoolHelper */
            $cartPoolHelper = $this->container->get('cart_pool_helper');

            $checkoutData = $cartPoolHelper->formatCheckout($data);
            $itemsData = $cartPoolHelper->formatItems($items);

            // TODO: Implementação será finalizada em tarefa posterior
        }

        return $this->render('cart.checkout', [
            'form' => $form->createView()
        ]);
    }

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

        $cartTotal = 0;
        $kits = [];

        /** @var CartHasKit $cartHasKit */
        foreach ($cartHasKits as $cartHasKit) {
            $kitTotal = $cartHasKit->getKit()->getPrice() * $cartHasKit->getQuantity();
            $cartTotal += $kitTotal;

            $kits[] = [
                'kit' => $cartHasKit->getKit(),
                'quantity' => $cartHasKit->getQuantity(),
                'total' => $kitTotal
            ];
        }

        return $this->render('cart.items', [
            'cart' => $cart,
            'kits' => $kits,
            'total' => $cartTotal,
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

        if ($cartHasKit) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = 'Este kit já foi adicionado ao carrinho';

            return $this->json([
                'message' => $message
            ], $status);
        }

        $cartHasKit = $cartHasKitManager->create();

        $quantity = $request->get('quantity');

        $cartHasKit->setKit($kit);
        $cartHasKit->setCart($cart);
        $cartHasKit->setQuantity($quantity);

        if ($cartHasKit->getKit() && $cartHasKit->getQuantity()) {
            $cartHasKitManager->save($cartHasKit);

            return $this->json([], Response::HTTP_OK);
        }

        if (!$cartHasKit->getKit() || !$cartHasKit->getQuantity()) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = !$cartHasKit->getKit() ? 'O kit não está disponível' : 'Quantidade indisponível';
        }

        return $this->json([
            'message' => $message
        ], $status);
    }

    /**
     * @Route("/{id}/quantity", name="quantity_kit")
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

    /**
     * @Security("has_role('ROLE_OWNER')")
     *
     * @Route("/{id}/remove_kit", name="cart_remove_kit")
     * @Method("delete")
     */
    public function removeKitAction(Kit $kit)
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        $cartHasKitManager = $this->manager('cart_has_kit');
        $cartHasKit = $cartHasKitManager->findOneBy([
            'cart' => $cart,
            'kit' => $kit
        ]);

        $cartHasKitManager->delete($cartHasKit);

        return $this->json([]);
    }
}