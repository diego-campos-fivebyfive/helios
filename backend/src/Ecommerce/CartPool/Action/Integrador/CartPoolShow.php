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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method};

class CartPoolShow extends AbstractController
{
    /**
     * @Route("/purchase/cart_pool/{id}", name="cart_pool_detail")
     * @Method("get")
     *
     * @param CartPool $cartPool
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(CartPool $cartPool)
    {
        /** @var CartPoolService $cartPoolService */
        $cartPoolService = $this->get('integrador.cart_pool_service');
        $this->denyAccessUnlessGranted('view', $cartPool);
        $result = $cartPoolService->show($cartPool);

        return $this->render('ecommerce/cart-pool/integrador/detail.html.twig', $result);
    }
}
