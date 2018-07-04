<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\CartPool\Action\PublicAccess;

use AppBundle\Controller\AbstractController;
use Ecommerce\CartPool\Service\GetnetService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{Route, Method};
use Symfony\Component\HttpFoundation\Request;

class PaymentBilletRegisterCallback extends AbstractController
{
    /**
     * @Route("/payment/billet/register", name="payment_callback_billet_register")
     * @Method("get")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function __invoke(Request $request)
    {
        /** @var GetnetService $getnetService */
        $getnetService = $this->get('getnet_service');

        $cartPoolId = $request->query->getInt('order_id');
        $queryParams = $request->query->all();

        $status = $getnetService->processPaymentBilletRegister($cartPoolId, $queryParams);
        return $this->json([], $status);
    }
}
