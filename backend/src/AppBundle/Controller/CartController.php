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
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("twig/cart")
 *
 * @Breadcrumb("Carrinho de compras")
 * @Security("has_role('ROLE_OWNER')")
 */
class CartController extends AbstractController
{
    /**
     * @Breadcrumb("Confirmação")
     * @Route("/checkout", name="cart_checkout")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCheckoutAction(Request $request)
    {
        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $this->getInfo($form);

            $this->updateCheckout($data);

            $numbers = [
                "phone" => $form->get('phone')->getData(),
                "document" => $form->get('document')->getData(),
            ];

            $kits = $data['kits'];
            unset($data['kits']);

            /** @var CartPoolHelper $cartPoolHelper */
            $cartPoolHelper = $this->container->get('cart_pool_helper');

            $account = $this->account();

            $cartPool = $cartPoolHelper->findOrCreateCartPool($account);

            $cartPoolHelper->updateCartPool($cartPool, $account);

            $shipping = $this->formatShipping($data);

            return $this->render('cart.confirmation', [
                'account' => $cartPool->getAccount(),
                'data' => $data,
                'shipping' => $shipping,
                'kits' => $kits,
                'numbers' => $numbers,
                'cartPoolId' => $cartPool->getId()
            ]);
        }

        $this->setDataForm($form);

        return $this->render('cart.checkout', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/items", name="cart_items")
     * @Method("get")
     */
    public function getCartItemsAction()
    {
        $cartHasKits = $this->getCartHasKits();

        $cartTotal = 0;
        $kits = [];

        /** @var CartHasKit $cartHasKit */
        foreach ($cartHasKits as $cartHasKit) {
            if ($cartHasKit->getKit()->isAvailable()) {
                $kitTotal = $cartHasKit->getKit()->getPrice() * $cartHasKit->getQuantity();
                $cartTotal += $kitTotal;

                $kits[] = [
                    'kit' => $cartHasKit->getKit(),
                    'quantity' => $cartHasKit->getQuantity(),
                    'total' => $kitTotal
                ];
            }
        }

        return $this->render('cart.items', [
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
        return $this->render('cart.show');
    }

    /**
     * @Route("/{id}/add_kit", name="cart_add_kit")
     * @Method("post")
     */
    public function addKitAction(Request $request, Kit $kit)
    {
        /** @var Cart $cart */
        $cart = $this->getCart();

        $cartHasKitManager = $this->manager('cart_has_kit');

        /** @var CartHasKit $cartHasKit */
        $cartHasKit = $cartHasKitManager->findOneBy([
            'cart' => $cart,
            'kit' => $kit
        ]);

        $status = Response::HTTP_OK;
        $message = 'Kit adicionado com sucesso';

        $quantity = $request->get('quantity');

        if ($cartHasKit) {
            $totalQuantity = $quantity + $cartHasKit->getQuantity();

            if ($totalQuantity > $cartHasKit->getKit()->getStock()) {
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = 'Quantidade indisponível';
            } else {
                $cartHasKit->setQuantity($totalQuantity);
                $cartHasKitManager->save($cartHasKit);

                $message = 'Quantidade do kit atualizada no carrinho';
            }

            return $this->json([
                'message' => $message
            ], $status);
        }

        $cartHasKit = $cartHasKitManager->create();

        $cartHasKit->setKit($kit);
        $cartHasKit->setCart($cart);
        $cartHasKit->setQuantity($quantity);

        if ($cartHasKit->getKit() && $cartHasKit->getQuantity()) {
            $cartHasKitManager->save($cartHasKit);

            return $this->json([
                'message' => $message
            ]);
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
        /** @var Cart $cart */
        $cart = $this->getCart();

        $quantity = $request->request->getInt('quantity');

        if ($kit->getStock() >= $quantity) {

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
        /** @var Cart $cart */
        $cart = $this->getCart();

        $cartHasKitManager = $this->manager('cart_has_kit');
        $cartHasKit = $cartHasKitManager->findOneBy([
            'cart' => $cart,
            'kit' => $kit
        ]);

        $cartHasKitManager->delete($cartHasKit);

        return $this->json([]);
    }

    /**
     * @Security("has_role('ROLE_OWNER')")
     *
     * @Route("/clear_cart", name="clear_cart")
     * @Method("delete")
     */
    public function clearCartAction()
    {
        /** @var Cart $cart */
        $cart = $this->getCart();

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->container->get('cart_pool_helper');

        $cartPoolHelper->clearCart($cart);

        return $this->json([]);
    }

    /**
     * @return Cart|mixed|object
     */
    private function createCart()
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->create();

        $cart->setAccount($this->account());

        $cartManager->save($cart);

        return $cart;
    }

    /**
     * @param $data
     */
    private function updateCheckout($data)
    {
        /** @var Cart $cart */
        $cart = $this->getCart();

        $manager = $this->manager('cart');

        unset($data['items'], $data['kits'], $data['amount'], $data['token'], $data['customerId']);

        $cart->setCheckout($data);

        $manager->save($cart);
    }

    /**
     * @param Form $form
     */
    private function setDataForm(Form &$form)
    {
        /** @var Cart $cart */
        $cart = $this->getCart();

        if ($checkout = $cart->getCheckout()) {
            $shipping = json_decode($checkout['shipping'], true)[0];

            $form->get('firstName')->setData($checkout['firstName']);
            $form->get('lastName')->setData($checkout['lastName']);
            $form->get('documentType')->setData($checkout['documentType']);
            $form->get('document')->setData($checkout['documentNumber']);
            $form->get('email')->setData($checkout['email']);
            $form->get('phone')->setData($checkout['phone']);
            $form->get('postcode')->setData($checkout['zipcode']);
            $form->get('state')->setData($checkout['state']);
            $form->get('city')->setData($checkout['city']);
            $form->get('neighborhood')->setData($checkout['neighborhood']);
            $form->get('street')->setData($checkout['street']);
            $form->get('number')->setData($checkout['number']);
            $form->get('complement')->setData($checkout['complement']);
            $form->get('differentDelivery')->setData($checkout['differentDelivery']);
            $form->get('shippingName')->setData($shipping['name']);
            $form->get('shippingEmail')->setData($shipping['email']);
            $form->get('shippingPhone')->setData($shipping['phone_number']);
            $form->get('shippingPostcode')->setData($shipping['address']['postal_code']);
            $form->get('shippingState')->setData($shipping['address']['state']);
            $form->get('shippingCity')->setData($shipping['address']['city']);
            $form->get('shippingNeighborhood')->setData($shipping['address']['district']);
            $form->get('shippingStreet')->setData($shipping['address']['street']);
            $form->get('shippingNumber')->setData($shipping['address']['number']);
            $form->get('shippingComplement')->setData($shipping['address']['complement']);
        }
    }

    /**
     * @return Cart
     */
    private function getCart()
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        return $cart ?? $this->createCart();
    }

    /**
     * @return array
     */
    private function getCartHasKits()
    {
        $cart = $this->getCart();

        $cartHasKitManager = $this->manager('cart_has_kit');

        return $cartHasKitManager->findBy([
            'cart' => $cart
        ]);
    }

    /**
     * @return string
     */
    private function getToken()
    {
        /** @var Getnet $getNet */
        $getNet = new GetNet(GetNet::HOMOLOG);

        return "Bearer " . $getNet->getAccessToken();
    }

    /**
     * @param Form $form
     * @return array
     */
    private function getInfo(Form $form)
    {
        $cartHasKits = $this->getCartHasKits();

        $dataForm = $this->getData($form);

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->container->get('cart_pool_helper');

        $data = $cartPoolHelper->formatCheckout($dataForm);

        $items = $cartPoolHelper->formatItems($cartHasKits);
        $data['items'] = json_encode($items);

        $cartTotal = 0;
        $kits = [];

        /** @var CartHasKit $cartHasKit */
        foreach ($cartHasKits as $cartHasKit) {
            if ($cartHasKit->getKit()->isAvailable()) {
                $kitTotal = $cartHasKit->getKit()->getPrice() * $cartHasKit->getQuantity();
                $cartTotal += $kitTotal;

                $kits[] = [
                    'kit' => $cartHasKit->getKit(),
                    'quantity' => $cartHasKit->getQuantity(),
                    'total' => $kitTotal
                ];
            }
        }

        $data['kits'] = $kits;

        $data['amount'] = number_format($cartTotal, 2, '.', '');
        $data['token'] = $this->getToken();
        $data['customerId'] = $this->account()->getId();

        return $data;
    }

    /**
     * @param Form $form
     * @return array
     */
    private function getData(Form $form)
    {
        $keys = [
            "firstName",
            "lastName",
            "documentType",
            "email",
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
            "shippingPostcode",
            "shippingState",
            "shippingCity",
            "shippingNeighborhood",
            "shippingStreet",
            "shippingNumber",
            "shippingComplement",
        ];

        foreach ($keys as $key) {
            $dataForm[$key] = $form->get($key)->getData();
        }

        $formatKeys = [
            "document",
            "phone",
            "shippingPhone"
        ];

        foreach ($formatKeys as $key) {
            $data = $form->get($key)->getData();

            $dataForm[$key] = preg_replace("/[ ()-.\/]/",'', $data);
        }

        return $dataForm;
    }

    private function formatShipping($data)
    {
        $different = $data['differentDelivery'];
        $shipping = json_decode($data['shipping'], true)[0];

        return [
            "firstName" => $different ? $shipping['first_name'] : $data['firstName'],
            "lastName" => $different ? $shipping['name'] : $data['lastName'],
            "document" => $data['documentNumber'],
            "email" => $different ? $shipping['email'] : $data['email'],
            "phone" => $different ? $shipping['phone_number'] : $data['phone'],
            "postcode" => $different ? $shipping['address']['postal_code'] : $data['zipcode'],
            "state" => $different ? $shipping['address']['state'] : $data['state'],
            "city" => $different ? $shipping['address']['city'] : $data['city'],
            "neighborhood" => $different ? $shipping['address']['district'] : $data['neighborhood'],
            "street" => $different ? $shipping['address']['street'] : $data['street'],
            "number" => $different ? $shipping['address']['number'] : $data['number'],
            "complement" => $different ? $shipping['address']['complement'] : $data['firstName']
        ];

    }
}
