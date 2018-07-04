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

class CartKitCheck extends AbstractController
{
    /**
     * @Route("/check_cart_kits", name="check_cart_kits")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(Request $request)
    {
        /** @var CartService $cartService */
        $cartService = $this->get('integrador.cart_service');
        $kitsOutOfStock = $cartService->cartKitCheck($this->account());
        $status = empty($kitsOutOfStock) ? Response::HTTP_OK : Response::HTTP_FORBIDDEN;

        return $this->json($kitsOutOfStock, $status);
    }
}
