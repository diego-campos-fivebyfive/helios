<?php

namespace AppBundle\Service\Cart;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Kit\CartHasKit;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Manager\CartHasKitManager;
use AppBundle\Manager\CartManager;
use AppBundle\Manager\CartPoolManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CartPoolHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CartHasKitManager
     */
    private $cartHasKitManager;

    /**
     * cartPoolTransform constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->cartHasKitManager = $this->container->get('cart_has_kit_manager');
    }

    /**
     * @param $code
     * @param AccountInterface $account
     * @return CartPool
     */
    public function createCartPool($code, AccountInterface $account)
    {
        $cart = $this->getCart($account);

        $cartHasKits = $this->getCartHasKits($cart);

        $items = $this->formatItems($cartHasKits, true);

        $checkout = $cart->getCheckout();

        if ($items) {
            /** @var CartPoolManager $cartPoolManager */
            $cartPoolManager = $this->container->get('cart_pool_manager');

            /** @var CartPool $cartPool */
            $cartPool = $cartPoolManager->create();

            $cartPool->setCode($code);
            $cartPool->setAccount($account);
            $cartPool->setAmount($this->getAmount($cart));
            $cartPool->setItems($items);
            $cartPool->setCheckout($checkout);

            $cartPoolManager->save($cartPool);

            $this->clearCart($cart);

            return $cartPool;
        }

        return null;
    }

    /**
     * @param Cart $cart
     */
    public function clearCart(Cart $cart)
    {
        $cartHasKits = $this->getCartHasKits($cart);

        foreach ($cartHasKits as $cartHasKit) {
            $this->cartHasKitManager->delete($cartHasKit, false);
        }

        $this->cartHasKitManager->flush();
    }

    /**
     * @param array $items
     * @return array
     */
    public function formatItems(array $items, $cartPool = false)
    {
        $formatedItems = array_map(function (CartHasKit $item) use($cartPool) {
            $formatedItem = [
                'name' => $item->getKit()->getCode(),
                'description' => $item->getKit()->getDescription(),
                'value' => round($item->getKit()->getPrice(), 2),
                'quantity' => $item->getQuantity(),
                'sku' => $item->getKit()->getId(),
                'image' => $item->getKit()->getImage()
            ];

            if ($cartPool) {
                $formatedItem['power'] = $item->getKit()->getPower();
                $formatedItem['components'] = $item->getKit()->getComponents();
            }

            return $formatedItem;
        }, $items);

        return $formatedItems;
    }

    /**
     * @param array $checkout
     * @return array
     */
    public function formatCheckout(array $checkout)
    {
        $shipping = [
            [
                "first_name" => $checkout['firstName'],
                "name" => $checkout['shippingName'],
                "email" => $checkout['shippingEmail'],
                "phone_number" => $checkout['shippingPhone'],
                "shipping_amount" => 10,
                "address" => [
                    "street" => $checkout['shippingStreet'],
                    "complement" => $checkout['shippingComplement'],
                    "number" => $checkout['shippingNumber'],
                    "district" => $checkout['shippingNeighborhood'],
                    "city" => $checkout['shippingCity'],
                    "state" => $checkout['shippingState'],
                    "country" => "Brasil",
                    "postal_code" => str_replace("-", "", $checkout['shippingPostcode'])
                ]
            ]
        ];

        return [
            "firstName" => $checkout['firstName'],
            "lastName" => $checkout['lastName'],
            "documentType" => $checkout['documentType'],
            "documentNumber" => $checkout['document'],
            "email" => $checkout['email'],
            "phone" => $checkout['phone'],
            "street" => $checkout['street'],
            "number" => $checkout['number'],
            "complement" => $checkout['complement'],
            "neighborhood" => $checkout['neighborhood'],
            "city" => $checkout['city'],
            "state" => $checkout['state'],
            "zipcode" => $checkout['postcode'],
            "country" => "Brasil",
            "differentDelivery" => $checkout['differentDelivery'],
            "shipping" => json_encode($shipping)
        ];
    }

    /**
     * @param Cart $cart
     * @return mixed
     */
    private function getAmount(Cart $cart)
    {
        $cartHasKits = $this->getCartHasKits($cart);

        return array_reduce($cartHasKits, function ($carry, $cartHasKit) {
            $kit = $cartHasKit->getKit();
            $carry += $kit->getPrice() * $cartHasKit->getQuantity();
            return $carry;
        }, 0);
    }

    /**
     * @param AccountInterface $account
     * @return null|Cart
     */
    private function getCart(AccountInterface $account)
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->container->get('cart_manager');

        return $cartManager->findOneBy([
            'account' => $account
        ]);
    }

    /**
     * @param Cart $cart
     * @return array
     */
    private function getCartHasKits(Cart $cart)
    {
        return $this->cartHasKitManager->findBy([
            'cart' => $cart
        ]);
    }
}
