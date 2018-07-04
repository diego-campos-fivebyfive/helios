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

class CartKitRemove extends AbstractController
{
    /**
     * @Route("/{id}/remove_kit", name="cart_remove_kit")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("delete")
     *
     * @param Request $request
     * @param Kit $kit
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(Request $request, Kit $kit)
    {
        /** @var CartService $cartService */
        $cartService = $this->get('integrador.cart_service');
        $cartService->removeKit($kit, $this->account());

        return $this->json([]);
    }
}
