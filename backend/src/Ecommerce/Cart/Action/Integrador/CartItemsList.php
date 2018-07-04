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
use Ecommerce\Cart\Service\Integrador\CartCheckoutService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Security, Route, Method};
use Symfony\Component\HttpFoundation\Request;

class CartItemsList extends AbstractController
{
    /**
     * @Route("/items", name="cart_items")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        /** @var CartCheckoutService $cartCheckoutService */
        $cartCheckoutService = $this->get('integrador.cart_checkout_service');
        $items = $cartCheckoutService->itemsList($this->account());

        return $this->render('ecommerce/cart/integrador/items.html.twig', $items);
    }
}
