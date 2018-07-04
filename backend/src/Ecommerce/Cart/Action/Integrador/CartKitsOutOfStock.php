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
use Ecommerce\Kit\Entity\Kit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Security, Route, Method};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartKitsOutOfStock extends AbstractController
{
    /**
     * @Route("/cart_kits_out_of_stock", name="cart_kits_out_of_stock")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("get")
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        /** @var CartService $cartService */
        $cartService = $this->get('integrador.cart_service');
        $kitsOutOfStock = $cartService->cartKitCheck($this->account());

        return $this->render('ecommerce/cart/integrador/kits_out_of_stock.html.twig', [
            'kits' => $kitsOutOfStock
        ]);
    }
}
