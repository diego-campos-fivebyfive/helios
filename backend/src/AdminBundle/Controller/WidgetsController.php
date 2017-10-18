<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Order\OrderInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("widget")
 */
class WidgetsController extends AdminController
{
    /**
     * @var array
     */
    private $filters = [
        'member' => null,
        'account' => null,
        'date' => null,
        'at' => 'month'
    ];

    /**
     * @param $by
     * @return $this
     */
    public function at($at)
    {
        $this->filters['at'] = $at;

        return $this;
    }

    /**
     * @param \DateTime|null $date
     * @return $this
     */
    public function date(\DateTime $date = null)
    {
        if(!$date){
            $date = new \DateTime();
        }

        $this->filters['date'] = $date;

        return $this;
    }

    /**
     * @Route("/orderGrapichs", name="widget_orders")
     */
    public function orderGraphicAction(Request $request)
    {
        $today = new \DateTime;

        $group = $request->get('group', 'month');
        $year = $request->get('year', $today->format('Y'));
        $month = $request->get('month', $today->format('m'));
        $day = $today->format('d');

        $date = new \DateTime(sprintf('%s-%s-%s', $year, $month, $day));
        $lastDay = cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));

        $this->at($group);
        $this->date($date);

        $data = $this->getOrderFilter();

        // Defaults
        $groups = [];
        $limit = 'month' == $group ? $lastDay : 12;
        for ($i = 1; $i <= $limit; $i++) {
            $groups[$i] = [
                'power' => 0,
                'amount' => 0,
                'count' => 0
            ];
        }

        /** @var OrderInterface $order */
        foreach ($data as $order) {
            $index = 'month' == $group
                ? (int) $order->getCreatedAt()->format('d')
                : (int) $order->getCreatedAt()->format('m');

            $power = 0;
            $total = 0;

            foreach ($order->getChildrens() as $children){
                $total += $children->getTotal();
                $power += $children->getPower();
            }

            $groups[$index]['count'] += 1;
            $groups[$index]['power'] += $power;
            $groups[$index]['amount'] += $total;
        }

        return $this->json([
            'options' => [
                'last_day' => $lastDay
            ],
            'data' => $groups
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/summaryOrder", name="widgets_summary")
     */
    public function orderCountAction()
    {
        $summary = [
            'count' => 0,
            'amount' => 0,
            'power' => 0
        ];

        $ordersCollection = $this->getOrders();

        $orders = $ordersCollection->getQuery()->getResult();

        foreach ($orders as $order) {
            $power = 0;
            $total = 0;
            foreach ($order->getChildrens() as $children){
                $total += $children->getTotal();
                $power += $children->getPower();
            }
            $summary['count'] += 1;
            $summary['amount'] += $total;
            $summary['power'] += $power;
        }

        return $this->json([
            'data' => $summary
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/orders_status", name="status_orders")
     */
    public function ordersStatusAction()
    {
        $collection = $this->startDefaults();

        $ordersCollection = $this->getOrders();

        $orders = $ordersCollection->getQuery()->getResult();

        foreach ($orders as $order) {
            $power = 0;
            $total = 0;

            foreach ($order->getChildrens() as $children){
                $total += $children->getTotal();
                $power += $children->getPower();
            }

            switch ($order->getStatus()) {
                case OrderInterface::STATUS_PENDING:
                    $collection = $this->getArrayStatus($collection, OrderInterface::STATUS_PENDING, $total, $power);
                    break;
                case OrderInterface::STATUS_VALIDATED:
                    $collection = $this->getArrayStatus($collection, OrderInterface::STATUS_VALIDATED, $total, $power);
                    break;
                case OrderInterface::STATUS_APPROVED:
                    $collection = $this->getArrayStatus($collection, OrderInterface::STATUS_APPROVED, $total, $power);
                    break;
                case OrderInterface::STATUS_REJECTED:
                    $collection = $this->getArrayStatus($collection, OrderInterface::STATUS_REJECTED, $total, $power);
                    break;
                case OrderInterface::STATUS_DONE:
                    $collection = $this->getArrayStatus($collection, OrderInterface::STATUS_DONE, $total, $power);
                    break;
            }
        }

        return $this->render('admin/widget/status-orders.html.twig', [
            'collection' => $collection
        ]);
    }

    private function startDefaults()
    {
        $collection = [];
        foreach (Order::getStatusNames() as $key => $statusName) {
            $collection[$key] = [
                'count' => 0,
                'amount' => 0,
                'power' => 0,
                'status' => $statusName
            ];
        }
        unset($collection[0]);

        return $collection;
    }

    private function getArrayStatus($collection, $status, $total, $power)
    {
        $collection[$status]['count'] += 1;
        $collection[$status]['amount'] += $total;
        $collection[$status]['power'] += $power;

        return $collection;
    }

    private function queryBuilder()
    {
        $qb = $this->manager('order')->createQueryBuilder();

        $qb
            ->andWhere('o.parent is null')
            ->orderBy('o.id', 'desc')
            ->andWhere('o.status != 0');

        return $qb;
    }

    private function getOrders()
    {
        $member = $this->member();

        $qb = $this->queryBuilder();

        $includeStatus = [];
        if ($member->isPlatformFinancial()) {
            $includeStatus = [Order::STATUS_APPROVED, Order::STATUS_DONE];
        }

        if ($member->isPlatformAfterSales()) {
            $includeStatus = [Order::STATUS_DONE];
        }

        if(!empty($includeStatus)){
            $qb->andWhere($qb->expr()->in('o.status', $includeStatus));
        }

        if($member->isPlatformCommercial()){
            $qb
                ->andWhere('o.agent = :agent')
                ->setParameters([
                    'agent' => $member->getId()
                ])
            ;
        }

        return $qb;
    }

    private function getOrderFilter()
    {
        $qb = $this->getOrders();

        $date = $this->filters['date'];

        //  Add Date Reference
        if($date instanceof \DateTime){

            $start = $date->format('Y-01-01 00:00:00');
            $end = $date->format('Y-12-31 23:59:59');

            if('month' == $this->filters['at']){

                $lastDay = cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));

                $start = $date->format('Y-m-01 00:00:00');
                $end = $date->format(sprintf('Y-m-%d 23:59:59', $lastDay));
            }

            $qb->andWhere('o.createdAt >= :start')
                ->andWhere('o.createdAt <= :end')
                ->andWhere('o.parent is null')
                ->orderBy('o.id', 'desc')
                ->andWhere('o.status != 0')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * @Route("/widget_time", name="widget_time")
     */
    public function timelineWidgetAction()
    {
        $member = $this->member();

        $subscriptions = $this->manager('notification')->subscriptions($member, [
            'type' => Notification::TYPE_TIMELINE,
            'limit' => 6
        ]);

        return $this->render('admin/widget/timeline.html.twig', [
            'subscriptions' => $subscriptions
        ]);
    }
}
