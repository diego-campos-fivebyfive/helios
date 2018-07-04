<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Cart\Service\Integrador;

use AppBundle\Entity\AccountInterface;
use AppBundle\Service\Common\FormHelper;
use Ecommerce\Cart\Entity\Cart;
use Ecommerce\Cart\Entity\CartHasKit;
use Ecommerce\Cart\Manager\CartHasKitManager;
use Ecommerce\Cart\Manager\CartManager;
use Ecommerce\Kit\Entity\Kit;
use Ecommerce\Kit\Manager\KitManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

class CartService
{
    /**
     * @var CartManager
     */
    private $manager;

    /**
     * @var CartHasKitManager
     */
    private $cartHasKitManager;

    /**
     * @var KitManager
     */
    private $kitManager;

    /**
     * CartService constructor.
     * @param CartManager $manager
     * @param CartHasKitManager $cartHasKitManager
     * @param KitManager $kitManager
     */
    public function __construct(
        CartManager $manager,
        CartHasKitManager $cartHasKitManager,
        KitManager $kitManager
    ) {
        $this->manager = $manager;
        $this->cartHasKitManager = $cartHasKitManager;
        $this->kitManager = $kitManager;
    }

    /**
     * @param Kit $kit
     * @param AccountInterface $account
     * @param int $quantity
     * @param int $status
     * @return array
     */
    public function addKit(
        Kit $kit,
        AccountInterface $account,
        int $quantity,
        int &$status
    ) {
        /** @var Cart $cart */
        $cart = $this->getCart($account);

        /** @var CartHasKit $cartHasKit */
        $cartHasKit = $this->cartHasKitManager->findOneBy([
            'cart' => $cart,
            'kit' => $kit
        ]);

        $message = 'Kit adicionado com sucesso';

        if ($cartHasKit) {
            $totalQuantity = $quantity + $cartHasKit->getQuantity();

            if ($totalQuantity > $cartHasKit->getKit()->getStock()) {
                $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = 'Quantidade indisponível';
            } else {
                $cartHasKit->setQuantity($totalQuantity);
                $this->cartHasKitManager->save($cartHasKit);

                $message = 'Quantidade do kit atualizada no carrinho';
            }

            return ['message' => $message];
        }

        $cartHasKit = $this->cartHasKitManager->create();

        $cartHasKit->setKit($kit);
        $cartHasKit->setCart($cart);
        $cartHasKit->setQuantity($quantity);

        if ($cartHasKit->getKit() && $cartHasKit->getQuantity()) {
            $this->cartHasKitManager->save($cartHasKit);

            return ['message' => $message];
        }

        if (!$cartHasKit->getKit() || !$cartHasKit->getQuantity()) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = !$cartHasKit->getKit() ? 'O kit não está disponível' : 'Quantidade indisponível';
        }

        return ['message' => $message];
    }

    /**
     * @param Kit $kit
     * @param AccountInterface $account
     * @param int $quantity
     * @param int $status
     * @return array
     */
    public function updateKitQuantity(
        Kit $kit,
        AccountInterface $account,
        int $quantity,
        int &$status
    ) {
        /** @var Cart $cart */
        $cart = $this->getCart($account);

        if ($kit->getStock() >= $quantity) {
            $cartHasKit = $this->cartHasKitManager->findOneBy([
                'cart' => $cart,
                'kit' => $kit
            ]);

            $cartHasKit->setQuantity($quantity);
            $this->cartHasKitManager->save($cartHasKit);

            return [];
        }

        $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        return ['message' => 'Quantidade indisponível'];
    }

    /**
     * @param Kit $kit
     * @param AccountInterface $account
     */
    public function removeKit(Kit $kit, AccountInterface $account)
    {
        $cart = $this->getCart($account);

        $cartHasKit = $this->cartHasKitManager->findOneBy([
            'cart' => $cart,
            'kit' => $kit
        ]);

        $this->cartHasKitManager->delete($cartHasKit);
    }

    /**
     * @param AccountInterface $account
     */
    public function clear(AccountInterface $account)
    {
        /** @var Cart $cart */
        $cart = $this->getCart($account);

        $cartHasKits = $this->cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        foreach ($cartHasKits as $cartHasKit) {
            $this->cartHasKitManager->delete($cartHasKit, false);
        }

        $this->cartHasKitManager->flush();
    }

    /**
     * @param AccountInterface $account
     * @return Cart
     */
    public function getCart(AccountInterface $account)
    {
        /** @var Cart $cart */
        $cart = $this->manager->findOneBy([
            'account' => $account
        ]);

        return $cart ?? $this->createCart($account);
    }

    /**
     * @param AccountInterface $account
     * @return Cart
     */
    public function createCart(AccountInterface $account)
    {
        /** @var Cart $cart */
        $cart = $this->manager->create();
        $cart->setAccount($account);
        $this->manager->save($cart);

        return $cart;
    }

    /**
     * @param AccountInterface $account
     * @return array
     */
    public function cartKitCheck(AccountInterface $account)
    {
        $cart = $this->getCart($account);

        $cartKits = $this->cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        $kitsOutOfStock = [];

        /** @var CartHasKit $cartKit */
        foreach ($cartKits as $cartKit) {
            $kitId = $cartKit->getKit()->getId();

            /** @var Kit $kit */
            $kit = $this->kitManager->find($kitId);

            if ($cartKit->getQuantity() > $kit->getStock()) {
                $kitsOutOfStock[] = $cartKit;
            }
        }

        return $kitsOutOfStock;
    }

    /**
     * @param AccountInterface $account
     * @param Form $form
     */
    public function bindDataOnForm(AccountInterface $account, Form &$form)
    {
        $cart = $this->getCart($account);

        if ($checkout = $cart->getCheckout()) {
            $shipping = json_decode($checkout['shipping'], true)[0];

            $data = [
                'firstName' => $checkout['firstName'],
                'lastName' => $checkout['lastName'],
                'documentType' => $checkout['documentType'],
                'document' => $checkout['documentNumber'],
                'email' => $checkout['email'],
                'phone' => $checkout['phone'],
                'postcode' => $checkout['zipcode'],
                'state' => $checkout['state'],
                'city' => $checkout['city'],
                'neighborhood' => $checkout['neighborhood'],
                'street' => $checkout['street'],
                'number' => $checkout['number'],
                'complement' => $checkout['complement'],
                'differentDelivery' => $checkout['differentDelivery'],
                // Shipping
                'shippingFirstName' => $shipping['first_name'],
                'shippingLastName' => $shipping['name'],
                'shippingEmail' => $shipping['email'],
                'shippingPhone' => $shipping['phone_number'],
                'shippingPostcode' => $shipping['address']['postal_code'],
                'shippingState' => $shipping['address']['state'],
                'shippingCity' => $shipping['address']['city'],
                'shippingNeighborhood' => $shipping['address']['district'],
                'shippingStreet' => $shipping['address']['street'],
                'shippingNumber' => $shipping['address']['number'],
                'shippingComplement' => $shipping['address']['complement'],
            ];

            FormHelper::setDataForm($form, $data);
        }
    }

}
