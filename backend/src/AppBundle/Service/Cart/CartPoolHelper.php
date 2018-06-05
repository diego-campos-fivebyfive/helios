<?php

namespace AppBundle\Service\Cart;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Kit\CartHasKit;
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
     * @param AccountInterface $account
     * @return array
     */
    private function formatAccount(AccountInterface $account)
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
        $formatedItems = [];

        /** @var CartHasKit $item */
        foreach ($items as $item) {
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

            $formatedItems[] = $formatedItem;
        }

        return $formatedItems;
    }

    /**
     * @param array $checkout
     * @return array
     */
    public function formatCheckout(array $checkout)
    {
        return [
            "first_name" => $checkout['firstName'],
            "name" => $checkout['firstName'] . " " . $checkout['lastName'],
            "email" => $checkout['email'],
            "phone_number" => $checkout['phone'],
            "shipping_amount" => 10,
            "address" => [
                "street" => $checkout['differentDelivery'] ? $checkout['shippingStreet'] : $checkout['street'],
                "complement" => $checkout['differentDelivery'] ? $checkout['shippingComplement'] : $checkout['complement'],
                "number" => $checkout['differentDelivery'] ? $checkout['shippingNumber'] : $checkout['number'],
                "district" => $checkout['differentDelivery'] ? $checkout['shippingNeighborhood'] : $checkout['neighborhood'],
                "city" => $checkout['differentDelivery'] ? $checkout['shippingCity'] : $checkout['city'],
                "state" => $checkout['differentDelivery'] ? $checkout['shippingState'] : $checkout['state'],
                "country" => "Brasil",
                "postal_code" => $checkout['differentDelivery']
                    ? str_replace("-", "", $checkout['shippingPostcode'])
                    : str_replace("-", "", $checkout['postcode'])
            ]
        ];
    }
}
