<?php

namespace Ecommerce\CartPool\Service\Integrador;

use AppBundle\Entity\AccountInterface;
use Ecommerce\Cart\Entity\Cart;
use Ecommerce\Cart\Entity\CartHasKit;
use Ecommerce\CartPool\Entity\CartPool;
use Ecommerce\Cart\Manager\CartHasKitManager;
use Ecommerce\Cart\Manager\CartManager;
use Ecommerce\CartPool\Manager\CartPoolManager;
use Doctrine\ORM\QueryBuilder;

class CartPoolHelper
{
    /**
     * @var CartPoolManager
     */
    private $manager;

    /**
     * @var CartHasKitManager
     */
    private $cartHasKitManager;

    /**
     * @var CartManager
     */
    private $cartManager;

    /**
     * CartPoolHelper constructor.
     * @param CartHasKitManager $cartHasKitManager
     * @param CartPoolManager $manager
     * @param CartManager $cartManager
     */
    public function __construct(
        CartHasKitManager $cartHasKitManager,
        CartPoolManager $manager,
        CartManager $cartManager
    ) {
        $this->manager = $manager;
        $this->cartHasKitManager = $cartHasKitManager;
        $this->cartManager = $cartManager;
    }

    /**
     * @param AccountInterface $account
     * @return CartPool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOrCreateCartPool(AccountInterface $account)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->manager->createQueryBuilder();

        $qb
            ->andWhere($qb->expr()->eq('c.account', $account->getId()))
            ->andWhere($qb->expr()->eq('c.confirmed', $qb->expr()->literal(false)));

        /** @var CartPool $cartPool */
        $cartPool = $qb->getQuery()->getOneOrNullResult();

        return $cartPool ? $cartPool : $this->generateCartPool($account);
    }

    /**
     * @param CartPool $cartPool
     * @param AccountInterface $account
     */
    public function updateCartPool(CartPool $cartPool, AccountInterface $account)
    {
        $cart = $this->getCart($account);

        $cartHasKits = $this->getCartHasKits($cart);

        $items = $this->formatItems($cartHasKits, true);

        $checkout = $cart->getCheckout();

        $cartPool->setAmount($this->getAmount($cart));
        $cartPool->setItems($items);
        $cartPool->setCheckout($checkout);

        $this->manager->save($cartPool);
    }

    /**
     * @param AccountInterface $account
     */
    public function clearCart(AccountInterface $account)
    {
        $cart = $this->getCart($account);
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
                "first_name" => $checkout['shippingFirstName'],
                "name" => $checkout['shippingLastName'],
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
     * @return null|object
     */
    private function getCart(AccountInterface $account)
    {
        return $this->cartManager->findOneBy([
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

    /**
     * @param AccountInterface $account
     * @return CartPool
     */
    private function generateCartPool(AccountInterface $account)
    {
        /** @var CartPool $cartPool */
        $cartPool = $this->manager->create();
        $cartPool
            ->setAccount($account)
            ->setStatus(CartPool::STATUS_CREATED)
            ->setConfirmed(false);

        $this->manager->save($cartPool);

        return $cartPool;
    }
}
