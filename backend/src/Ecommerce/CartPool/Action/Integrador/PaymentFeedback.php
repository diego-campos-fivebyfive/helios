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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Security, Route, Method};
use Symfony\Component\HttpFoundation\Request;

class PaymentFeedback extends AbstractController
{
    /**
     * @Route("/purchase/payment_feedback", name="payment_feedback")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        return $this->render('ecommerce/cart-pool/integrador/feedback.html.twig', []);
    }
}
