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
use Ecommerce\Cart\Service\Integrador\CartCheckoutService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Security, Route, Method};
use Symfony\Component\HttpFoundation\Request;

class CartCheckoutConfirm extends AbstractController
{
    /**
     * @Route("/checkout")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("post")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        /** @var CartCheckoutService $cartCheckoutService */
        $cartCheckoutService = $this->get('integrador.cart_checkout_service');

        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account = $this->account();
            $data = $cartCheckoutService->confirm($form, $account);

            return $this->render('ecommerce/cart/integrador/confirmation.html.twig', $data);
        }

        return $this->render('ecommerce/cart/integrador/checkout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
