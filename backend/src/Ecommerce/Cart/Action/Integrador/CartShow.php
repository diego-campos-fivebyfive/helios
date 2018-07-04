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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method};
use Symfony\Component\HttpFoundation\Request;

class CartShow extends AbstractController
{
    /**
     * @Route("/show", name="cart_show")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        return $this->render('ecommerce/cart/integrador/show.html.twig');
    }
}
