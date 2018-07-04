<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\CartPool\Service;

use AppBundle\Service\Order\OrderTransformer;
use Ecommerce\CartPool\Entity\CartPool;
use Ecommerce\CartPool\Manager\CartPoolManager;
use Symfony\Component\HttpFoundation\Response;

class GetnetService
{
    /**
     * @var CartPoolManager
     */
    private $cartPoolManager;

    /**
     * @var OrderTransformer
     */
    private $orderTransformer;

    public function __construct(CartPoolManager $cartPoolManager, OrderTransformer $orderTransformer)
    {
        $this->cartPoolManager = $cartPoolManager;
        $this->orderTransformer = $orderTransformer;
    }

    /**
     * @param int $cartPoolId
     * @param array $queryParams
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function processPaymentCard(int $cartPoolId, array $queryParams)
    {
        /** @var CartPool $pool */
        $pool = $this->cartPoolManager->find($cartPoolId);

        $callback = $this->formatCallback($queryParams, 'card');
        $result = $this->processCardCallback($callback, $pool);

        return $this->processResponse($result, $pool);
    }

    /**
     * @param int $cartPoolId
     * @param array $queryParams
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function processPaymentBilletRegister(int $cartPoolId, array $queryParams)
    {
        /** @var CartPool $pool */
        $pool = $this->cartPoolManager->find($cartPoolId);

        $callback = $this->formatCallback($queryParams, 'billet');
        $result = $this->processBilletCallback($callback, $pool);

        return $this->processResponse($result, $pool);
    }

    /**
     * @param $billetId
     * @param array $queryParams
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    public function processPaymentBillet($billetId, array $queryParams)
    {
        /** @var CartPool $pool */
        $pool = $this->cartPoolManager->findOneBy([
            'billetId' => $billetId
        ]);

        $callback = $this->formatCallback($queryParams, 'paymentBillet');
        $result = $this->processPaymentBilletCallback($callback, $pool);

        return $this->processResponse($result, $pool);
    }

    /**
     * @param $result
     * @param $pool
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\RuntimeException
     */
    private function processResponse($result, $pool)
    {
        if ($result['processed']) {
            $this->cartPoolManager->save($pool);

            if ($result['transform']) {
                $this->orderTransformer->transformFromCartPool($pool);
            }

            return Response::HTTP_OK;
        }

        return Response::HTTP_BAD_REQUEST;
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

    /**
     * @param array $callback
     * @param CartPool $cartPool
     * @return array
     */
    private function processCardCallback(array $callback, CartPool $cartPool)
    {
        if ($callback && $cartPool) {
            $status = CartPool::getCardStatuses()[$callback['status']];

            $transform = ($callback['status'] === 'APPROVED') && ($cartPool->getStatus() !== CartPool::STATUS_CARD_APPROVED);

            $cartPool->addCallback($callback);
            $cartPool->setStatus($status);

            return [
                'processed' => true,
                'transform' => $transform
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
     * @param CartPool $cartPool
     * @return array
     */
    private function processPaymentBilletCallback(array $callback, CartPool $cartPool)
    {
        if ($callback && $cartPool) {
            $status = CartPool::getBilletStatuses()[$callback['status']];

            $transform = ($callback['status'] === 'PAID') && ($cartPool->getStatus() !== CartPool::STATUS_BILLET_PAID);

            $cartPool->addCallback($callback);
            $cartPool->setStatus($status);

            return [
                'processed' => true,
                'transform' => $transform
            ];
        }

        return [
            'processed' => false,
            'transform' => false
        ];
    }
}
