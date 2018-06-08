<?php

namespace AppBundle\Service\Cart;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Kit\CartHasKit;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Manager\CartPoolManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CartPoolHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * cartPoolTransform constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $code
     * @param AccountInterface $account
     * @param array $items
     * @param array $checkout
     * @return CartPool
     */
    public function createCartPool($code, AccountInterface $account, array $items, array $checkout)
    {
        if ($items) {
            /** @var CartPoolManager $cartPoolManager */
            $cartPoolManager = $this->container->get('cart_pool_manager');

            /** @var CartPool $cartPool */
            $cartPool = $cartPoolManager->create();

            $cartPool->setCode($code);
            $cartPool->setAccount($account);
            $cartPool->setItems($items);
            $cartPool->setCheckout($checkout);

            $cartPoolManager->save($cartPool);

            return $cartPool;
        }

        return null;
    }

    /**
     * @param Cart $cart
     */
    public function clearCart(Cart $cart)
    {
        /** @var CartHasKit $cartHasKitManager */
        $cartHasKitManager = $this->container->get('cart_has_kit_manager');

        $cartHasKits = $cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        foreach ($cartHasKits as $cartHasKit) {
            $cartHasKitManager->delete($cartHasKit, false);
        }

        $cartHasKitManager->flush();
    }

    /**
     * @param array $items
     * @return array
     */
    public function formatItems(array $items, $checkout = true)
    {
        $formatedItems = array_map(function (CartHasKit $item) use($checkout) {
            $formatedItem = [
                'name' => $item->getKit()->getCode(),
                'description' => $item->getKit()->getDescription(),
                'value' => round($item->getKit()->getPrice(), 2),
                'quantity' => $item->getQuantity(),
                'sku' => $item->getKit()->getId(),
                'image' => $item->getKit()->getImage()
            ];

            if (!$checkout) {
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
}
