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
     * @Route("/card", name="payment_callback_card")
     * @Method("get")
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPaymentCardAction(Request $request)
    {
        /** @var CartPoolManager $cartPoolManager */
        $cartPoolManager = $this->container->get('cart_pool_manager');

        $code = $request->query->get('order_id');

        /** @var CartPool $pool */
        $pool = $cartPoolManager->findOneBy([
            'code' => $code
        ]);

        $callback = $this->formatCallback($request->query->all(), 'card');
        $result = $this->processCardCallback($callback, $pool);

        if ($result['processed']) {
            $cartPoolManager->save($pool);

            if ($result['transform']) {
                /** @var OrderTransformer $orderTransformer */
                $orderTransformer = $this->container->get('order_transformer');

                $orderTransformer->transformFromCartPool($pool);
            }

            return $this->json();
        }

        return $this->json([], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/billet/register", name="payment_callback_billet_register")
     * @Method("get")
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPaymentBilletRegisterAction(Request $request)
    {
        /** @var CartPoolManager $cartPoolManager */
        $cartPoolManager = $this->container->get('cart_pool_manager');

        $code = $request->query->get('order_id');

        /** @var CartPool $pool */
        $pool = $cartPoolManager->findOneBy([
            'code' => $code
        ]);

        $callback = $this->formatCallback($request->query->all(), 'billet');
        $result = $this->processBilletCallback($callback, $pool);

        if ($result['processed']) {
            $cartPoolManager->save($pool);

            if ($result['transform']) {
                /** @var OrderTransformer $orderTransformer */
                $orderTransformer = $this->container->get('order_transformer');

                $orderTransformer->transformFromCartPool($pool);
            }

            return $this->json();
        }

        return $this->json([], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/billet", name="payment_callback_billet")
     * @Method("get")
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPaymentBilletAction(Request $request)
    {
        /** @var CartPoolManager $cartPoolManager */
        $cartPoolManager = $this->container->get('cart_pool_manager');

        $billetId = $request->query->get('id');

        /** @var CartPool $pool */
        $pool = $cartPoolManager->findOneBy([
            'billetId' => $billetId
        ]);

        $callback = $this->formatCallback($request->query->all(), 'paymentBillet');
        $result = $this->processPaymentBilletCallback($callback, $pool);

        if ($result['processed']) {
            $cartPoolManager->save($pool);

            if ($result['transform']) {
                /** @var OrderTransformer $orderTransformer */
                $orderTransformer = $this->container->get('order_transformer');

                $orderTransformer->transformFromCartPool($pool);
            }

            return $this->json();
        }

        return $this->json([], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param array $callback
     * @param CartPool $cartPool
     * @return array
     */
    private function processCardCallback(array $callback, CartPool $cartPool)
    {

        if ($callback && $cartPool) {
            $status = CartPool::getCardStatuses()[$callback['status']];

            $cartPool->addCallback($callback);
            $cartPool->setStatus($status);

            return [
                'processed' => true,
                'transform' => $callback['status'] === 'APPROVED'
            ];
        }

        return [
            'processed' => false,
            'transform' => false
        ];
    }

    /**
     * @param array $callback
     * @param CartPool $cartPool
     * @return array
     */
    private function processPaymentBilletCallback(array $callback, CartPool $cartPool)
    {
        if ($callback && $cartPool) {
            $status = CartPool::getBilletStatuses()[$callback['status']];

            $cartPool->addCallback($callback);
            $cartPool->setStatus($status);

            return [
                'processed' => true,
                'transform' => $callback['status'] === 'PAID'
            ];
        }

        return [
            'processed' => false,
            'transform' => false
        ];
    }

    /**
     * @param array $callback
     * @param CartPool $cartPool
     * @return array
     */
    private function processBilletCallback(array $callback, CartPool $cartPool)
    {
        if ($callback && $cartPool) {
            $status = CartPool::getBilletStatuses()[$callback['status']];

            $cartPool->addCallback($callback);
            $cartPool->setStatus($status);
            $cartPool->setBilletId($callback['id']);

            return [
                'processed' => true,
                'transform' => false
            ];
        }

        return [
            'processed' => false,
            'transform' => false
        ];
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
            'payment_type',
            'order_id',
            'id',
            'amount',
            'status',
            'bank',
            'our_number',
            'typeful_line',
            'issue_date'
        ];

        $paymentBilletKeys = [
            'id',
            'payment_date',
            'amount',
            'status'
        ];

        $keys = [
            'card' => $cardKeys,
            'billet' => $billetKeys,
            'paymentBillet' => $paymentBilletKeys
        ];

        if (array_diff($keys[$mode], array_keys($callback))) {
            return null;
        }

        return $this->getFormat($mode, $callback);
    }

    /**
     * @param $mode
     * @param array $callback
     * @return mixed
     */
    private function getFormat($mode, array $callback)
    {
        $format = [];

        switch ($mode) {
            case 'card':
                $format = [
                    'acquirerTransactionId' => $callback['acquirer_transaction_id'],
                    'amount' => $callback['amount'],
                    'authorizationTimestamp' => $callback['authorization_timestamp'],
                    'customerId' => $callback['customer_id'],
                    'numberInstallments' => $callback['number_installments'],
                    'orderId' => $callback['order_id'],
                    'paymentId' => $callback['payment_id'],
                    'paymentType' => $callback['payment_type'],
                    'status' => $callback['status']
                ];
                break;
            case 'billet':
                $format = [
                    'paymentType' =>  $callback['payment_type'],
                    'orderId' => $callback['order_id'],
                    'id' => $callback['id'],
                    'amount' => $callback['amount'],
                    'status' => $callback['status'],
                    'bank' => $callback['bank'],
                    'ourNumber' => $callback['our_number'],
                    'typefulLine' => $callback['typeful_line'],
                    'issueDate' => $callback['issue_date']
                ];
                break;
            case 'paymentBillet':
                $format = [
                    'id' => $callback['id'],
                    'paymentDate' => $callback['payment_date'],
                    'amount' => $callback['amount'],
                    'status' => $callback['status']
                ];
                break;
        }

        return $format;
    }
}
