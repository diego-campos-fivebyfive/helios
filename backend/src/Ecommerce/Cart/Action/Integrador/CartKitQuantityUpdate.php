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

class CartKitQuantityUpdate extends AbstractController
{
    /**
     * @Route("/{id}/quantity", name="quantity_kit")
     * @Method("put")
     * @Security("has_role('ROLE_OWNER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, Kit $kit)
    {
        /** @var CartService $cartService */
        $cartService = $this->get('integrador.cart_service');
        $status = Response::HTTP_OK;
        $quantity = $request->request->getInt('quantity');
        $response = $cartService->updateKitQuantity($kit, $this->account(), $quantity, $status);

        return $this->json($response, $status);
    }
}
