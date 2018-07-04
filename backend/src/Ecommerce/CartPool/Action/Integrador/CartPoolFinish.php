<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\CartPool\Action\Integrador;

use AppBundle\Controller\AbstractController;
use Ecommerce\CartPool\Entity\CartPool;
use Ecommerce\CartPool\Service\Integrador\CartPoolService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Security, Route, Method};

class CartPoolFinish extends AbstractController
{
    /**
     * @Route("/purchase/{id}/finish_cart_pool", name="cart_pool_finish")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("post")
     *
     * @param CartPool $cartPool
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(CartPool $cartPool)
    {
        /** @var CartPoolService $cartPoolService */
        $cartPoolService = $this->get('integrador.cart_pool_service');
        $cartPoolService->finish($cartPool, $this->account());

        return $this->json();
    }
}
