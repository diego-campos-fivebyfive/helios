<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Entity\Theme;
use AppBundle\Entity\Order\Order;
use AppBundle\Manager\CartPoolManager;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

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

        // TODO: Implementar transformação de pool em order quando $callback['status] = APPROVED usando o serviço
        if (isset($paymentType)) {
            $code = $request->query->get('order_id');

            $callback = $this->formatCallback($request->query->all(), 'card');

            /** @var CartPool $pool */
            $pool = $cartPoolManager->findOneBy([
                'code' => $code
            ]);

            if ($pool) {
                $pool->addCallback($callback);

                $cartPoolManager->save($pool);

                return JsonResponse::HTTP_OK;
            }

            return JsonResponse::HTTP_BAD_REQUEST;
        } else {
            $billetCode = $request->query->get('id');

            $callback = $this->formatCallback($request->query->all(), 'billet');

            // TODO: Usar serviço para buscar no pool pelo id do boleto($billetCode)
            /** @var CartPool $pool */
            $pool = null;

            if ($pool) {
                $pool->addCallback($callback);

                $cartPoolManager->save($pool);

                return JsonResponse::HTTP_OK;
            }

            return JsonResponse::HTTP_BAD_REQUEST;
        }
    }

    /**
     * @param array $callback
     * @param $mode
     * @return array
     */
    private function formatCallback(array $callback, $mode)
    {
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
