<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Cart\Action\Integrador;

use AppBundle\Controller\AbstractController;
use Ecommerce\Cart\Form\CheckoutType;
use AppBundle\Service\Common\FormHelper;
use Ecommerce\Cart\Entity\Cart;
use Ecommerce\Cart\Service\Integrador\CartService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Security, Route, Method};
use Symfony\Component\HttpFoundation\Request;

class CartCheckoutCreate extends AbstractController
{
    /**
     * @Route("/checkout", name="cart_checkout")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        /** @var CartService $cartService */
        $cartService = $this->get('integrador.cart_service');

        $form = $this->createForm(CheckoutType::class);

        /** @var Cart $cart */
        $cart = $cartService->getCart($this->account());

        if ($checkout = $cart->getCheckout()) {
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
            ];

            FormHelper::setDataForm($form, $data);

            $shipping = json_decode($checkout['shipping'], true)[0];

            $data = [
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

        return $this->render('ecommerce/cart/integrador/checkout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
