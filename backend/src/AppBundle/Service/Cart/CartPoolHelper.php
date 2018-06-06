<?php

namespace AppBundle\Service\Cart;

use AppBundle\Entity\AccountInterface;
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
     * @param $method
     * @param AccountInterface $account
     * @param array $items
     * @param array $checkout
     * @return CartPool
     */
    public function create($code, $method, AccountInterface $account, array $items, array $checkout)
    {
        if ($items) {
            /** @var CartPoolManager $cartPoolManager */
            $cartPoolManager = $this->container->get('cart_pool_manager');

            /** @var CartPool $cartPool */
            $cartPool = $cartPoolManager->create();

            $cartPool->setCode($code);
            $cartPool->setMethod($method);
            $cartPool->setAccount($account);
            $cartPool->setItems($items);
            $cartPool->setCheckout($checkout);

            $cartPoolManager->save($cartPool);

            return $cartPool;
        }

        return null;
    }

    /**
     * @param AccountInterface $account
     * @return array
     */
    public function formatAccount(AccountInterface $account)
    {
        return [
            'id' => $account->getId(),
            'firstname' => $account->getFirstname(),
            'lastname' => $account->getLastname(),
            'document' => $account->getDocument(),
            'extraDocument' => $account->getExtraDocument(),
            'email' => $account->getEmail(),
            'phone' => $account->getPhone(),
            'level' => $account->getLevel()
        ];
    }

    /**
     * @param array $items
     * @return array
     */
    public function formatItems(array $items, $checkout = true)
    {
        $formatedItems = array_map(function ($item) use($checkout) {
            $formatedItem = [
                'name' => $item->getKit()->getCode(),
                'description' => $item->getKit()->getDescription(),
                'value' => round($item->getKit()->getPrice(), 2),
                'quantity' => $item->getQuantity(),
                'sku' => $item->getKit()->getId()
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
            "first_name" => $checkout['firstName'],
            "name" => $checkout['shippingName'],
            "email" => $checkout['email'],
            "phone_number" => $checkout['phone'],
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
            "shipping" => json_encode($shipping),
            "differentDelivery" => $checkout['differentDelivery']
        ];
    }
}
