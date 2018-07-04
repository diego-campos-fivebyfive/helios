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
use Ecommerce\Cart\Service\Integrador\CartService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Security, Route, Method};

class CartClear extends AbstractController
{
    /**
     * @Route("/clear_cart", name="clear_cart")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("delete")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke()
    {
        /** @var CartService $cartService */
        $cartService = $this->get('integrador.cart_service');
        $cartService->clear($this->account());

        return $this->json([]);
    }
}
