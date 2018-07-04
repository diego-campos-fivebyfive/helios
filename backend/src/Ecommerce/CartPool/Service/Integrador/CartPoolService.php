<?php

namespace Ecommerce\CartPool\Service\Integrador;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\MemberInterface;
use Doctrine\ORM\QueryBuilder;
use Ecommerce\CartPool\Entity\CartPool;
use Ecommerce\CartPool\Manager\CartPoolManager;
use Knp\Component\Pager\PaginatorInterface;

class CartPoolService
{
    /**
     * @var CartPoolManager
     */
    private $manager;

    /**
     * @var CartPoolHelper
     */
    private $cartPoolHelper;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * CartPoolHelper constructor.
     * @param CartPoolManager $manager
     * @param CartPoolHelper $cartPoolHelper
     */
    public function __construct(
        CartPoolManager $manager,
        CartPoolHelper $cartPoolHelper,
        PaginatorInterface $paginator
    ) {
        $this->manager = $manager;
        $this->cartPoolHelper = $cartPoolHelper;
        $this->paginator = $paginator;
    }

    /**
     * @param CartPool $cartPool
     * @param AccountInterface $account
     */
    public function finish(CartPool $cartPool, AccountInterface $account)
    {
        $this->cartPoolHelper->clearCart($account);
        $cartPool->setConfirmed(true);
        $this->manager->save($cartPool);
    }

    /**
     * @param $status
     * @param AccountInterface $account
     * @param MemberInterface $member
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function listCartPools(
        $status,
        AccountInterface $account,
        MemberInterface $member,
        int $page,
        int $perPage = 10
    ) {
        $qb = $this->manager->createQueryBuilder();
        $qb->orderBy('c.id', 'desc');

        if (! $member->isPlatformUser()) {
            $qb->andWhere($qb->expr()->eq('c.account', $account->getId()));
        }

        if (-1 != $status) {
            $status = explode(',', $status);
            $arrayStatus = array_filter($status, 'strlen');
            if (!empty($arrayStatus)) {
                $qb->andWhere($qb->expr()->in('c.status', $arrayStatus));
            }
        }

        $getStatuses = function ($statusList, $arrayStatus) {
            $finalOptions = [];
            foreach ($statusList as $key => $status) {
                $finalOptions[$key] = [
                    'name' => $status,
                    'checked' => in_array($key, $arrayStatus)
                ];
            }

            return $finalOptions;
        };

        $pagination = $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $perPage
        );

        return [
            'pagination' => $pagination,
            'statusList' => $getStatuses(CartPool::getStatusNames(), $arrayStatus)
        ];
    }

    /**
     * @param CartPool $cartPool
     * @return array
     */
    public function show(CartPool $cartPool)
    {
        $cartPoolTotal = 0;
        $kits = [];

        foreach ($cartPool->getItems() as $item) {
            $kitTotal = $item['value'] * $item['quantity'];
            $cartPoolTotal += $kitTotal;

            $kits[] = [
                'item' => $item,
                'quantity' => $item['quantity'],
                'total' => $kitTotal
            ];
        }

        $shipping = json_decode($cartPool->getCheckout()['shipping'], true)[0];

        return [
            'cartPool' => $cartPool,
            'kits' => $kits,
            'total' => $cartPoolTotal,
            'shipping' => $shipping
        ];
    }
}
