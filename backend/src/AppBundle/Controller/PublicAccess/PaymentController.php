<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Manager\CartPoolManager;
use AppBundle\Service\Order\OrderTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("payment")
 */
class PaymentController extends AbstractController
{
    /**
     * @Route("/", name="payment_callback")
     * @Method("get")
     */
    public function getPaymentAction(Request $request)
    {
        $paymentType = $request->query->get('payment_type');

        /** @var CartPoolManager $cartPoolManager */
        $cartPoolManager = $this->container->get('cart_pool_manager');

        /** @var OrderTransformer $orderTransformer */
        $orderTransformer = $this->container->get('order_transformer');

        if (isset($paymentType)) {
            $code = $request->query->get('order_id');

            $callback = $this->formatCallback($request->query->all(), 'card');

            /** @var CartPool $pool */
            $pool = $cartPoolManager->findOneBy([
                'code' => $code
            ]);

            if ($pool && $callback) {
                $pool->addCallback($callback);

                $cartPoolManager->save($pool);

                if ($callback['status'] === 'APPROVED') {
                    $orderTransformer->transformFromCartPool($pool);
                }

                return $this->json();
            }

            return JsonResponse::create([], Response::HTTP_BAD_REQUEST);
        } else {
            $billetCode = $request->query->get('id');

            $callback = $this->formatCallback($request->query->all(), 'billet');

            // TODO: Usar serviço para buscar no pool pelo id do boleto($billetCode)
            /** @var CartPool $pool */
            $pool = null;

            if ($pool && $callback) {
                $pool->addCallback($callback);

                $cartPoolManager->save($pool);

                if ($callback['status'] === 'PAGO') {
                    $orderTransformer->transformFromCartPool($pool);
                }

                return $this->json();
            }

            return JsonResponse::create([], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $callback
     * @param $mode
     * @return array
     */
    private function formatCallback(array $callback, $mode)
    {
        $cardKeys = [
            'acquirer_transaction_id',
            'amount',
            'authorization_timestamp',
            'customer_id',
            'number_installments',
            'order_id',
            'payment_id',
            'payment_type',
            'status'
        ];

        $billetKeys = [
            'id',
            'payment_date',
            'amount',
            'status'
        ];

        if ($mode === 'card') {
            $keys = $cardKeys;
        } else {
            $keys = $billetKeys;
        }

        if (array_diff($keys, array_keys($callback))) {
            return null;
        }

        if ($mode === 'card') {
            return [
                'acquirer_transaction_id' => $callback['acquirer_transaction_id'],
                'amount' => $callback['amount'],
                'authorization_timestamp' => $callback['authorization_timestamp'],
                'customer_id' => $callback['customer_id'],
                'number_installments' => $callback['number_installments'],
                'order_id' => $callback['order_id'],
                'payment_id' => $callback['payment_id'],
                'payment_type' => $callback['payment_type'],
                'status' => $callback['status']
            ];
        }

        return [
            'id' => $callback['id'],
            'payment_date' => $callback['payment_date'],
            'amount' => $callback['amount'],
            'status' => $callback['status']
        ];
    }
}
