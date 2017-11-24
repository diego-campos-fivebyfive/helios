<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Order\OrderInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
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
     * @param $at
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
    public function filters(\DateTime $date = null, $status)
    {
        if(!$date){
            $date = new \DateTime();
        }

        $this->filters['date'] = $date;
        $this->filters['status'] = $status;

        return $this;
    }

    /**
     * @Route("/orderGrapichs", name="widget_orders")
     */
    public function orderGraphicAction(Request $request)
    {
        $today = new \DateTime;

        $status = $request->get('status');
        if ($status == "null")
            $status = null;
        else
            $status = array_merge(explode(",", $status));

        $group = $request->get('group', 'month');
        $year = $request->get('year', $today->format('Y'));
        $month = $request->get('month', $today->format('m'));
        $day = $today->format('d');

        $date = new \DateTime(sprintf('%s-%s-%s', $year, $month, $day));
        $lastDay = cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));

        $this->at($group);
        $this->filters($date, $status);

        $qb = $this->getOrderFilter();

        $qb
            ->select('sum(o.total) as total, sum(o.power) as power, o.createdAt')
            ->groupBy('o.id')
        ;

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

        $orders = $qb->getQuery()->getResult();

        /** @var OrderInterface $order */
        foreach ($orders as $order) {

            /** @var \DateTime $createdAt */
            $createdAt = $order['createdAt'];

            $index = 'month' == $group
                ? (int) $createdAt->format('d')
                : (int) $createdAt->format('m');

            $groups[$index]['count'] += 1;
            $groups[$index]['power'] += $order['power'];
            $groups[$index]['amount'] += $order['total'];
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
        $qb = $this->getOrders();

        $qb->select($qb->expr()->count('o.id'));

        $count = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        $amount = $qb->select('sum(o.total)')->getQuery()->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        $qb->select('sum(c.power)')
            ->join('o.childrens', 'c');

        $power = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        $summary = [
            'count' => (int) $count,
            'amount' => (float) $amount,
            'power' => (float) $power
        ];

        return $this->json([
            'data' => $summary
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/orders_status", name="status_orders")
     */
    public function ordersStatusAction()
    {
        $collection = $this->getOrdersStatus();

        return $this->render('admin/widget/status-orders.html.twig', [
            'collection' => $collection
        ]);
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

    /**
     * @return array
     */
    private function startDefaults()
    {
        $collection = [];
        foreach (Order::getStatusNames() as $key => $statusName) {
            $collection[$key] = [
                'count' => 0,
                'amount' => 0,
                'power' => 0,
                'medValue' => 0,
                'medPower' => 0,
                'status' => $statusName
            ];
        }
        unset($collection[0]);

        return $collection;
    }

    /**
     * @return array
     */
    private function getOrdersStatus()
    {
        $collection = $collection = $this->startDefaults();

        $qb = $this->getOrders();
        $qb
            ->select('o.status as status, sum(o.total) as total, sum(o.power) as power')
            ->groupBy('o.id')
        ;

        $orders = $qb->getQuery()->getResult();

        foreach ($orders as $order) {
            if (in_array($order['status'], Order::getStatusList())) {
                $collection[$order['status']]['count'] += 1;
                $collection[$order['status']]['amount'] += $order['total'];
                $collection[$order['status']]['power'] += $order['power'];
            }
        }

        foreach ($collection as $key => $item) {
            if ($collection[$key]['count']) {
                if ($collection[$key]['amount'])
                    $collection[$key]['medValue'] = $collection[$key]['amount'] / $collection[$key]['count'];
                if ($collection[$key]['power'])
                    $collection[$key]['medPower'] = $collection[$key]['power'] / $collection[$key]['count'];
            }
        }

        return $collection;
    }

    /**
     * @return QueryBuilder
     */
    private function queryBuilder()
    {
        $qb = $this->manager('order')->createQueryBuilder();

        $qb
            ->andWhere('o.parent is null')
            ->orderBy('o.id', 'desc')
            ->andWhere('o.status != 0');

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    private function getOrders()
    {
        $member = $this->member();

        $qb = $this->queryBuilder();

        $excludeStatus = [];
        if($member->isPlatformFinancial())
            $excludeStatus = [Order::STATUS_BUILDING, Order::STATUS_PENDING, Order::STATUS_VALIDATED];

        if($member->isPlatformLogistic() || $member->isPlatformAfterSales())
            $excludeStatus = [Order::STATUS_BUILDING, Order::STATUS_PENDING, Order::STATUS_VALIDATED, Order::STATUS_APPROVED];


        if(!empty($excludeStatus)){
            $qb->andWhere($qb->expr()->notIn('o.status', $excludeStatus));
        }

        if($member->isPlatformCommercial()){
            $qb
                ->andWhere('o.agent = :agent')
                ->setParameters([
                    'agent' => $member->getId()
                ])
            ;
        }

        if ($member->isPlatformExpanse()) {
            $qb->andWhere($qb->expr()->in('o.state', $member->getAttributes()['states']));
        }

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    private function getOrderFilter()
    {
        $qb = $this->getOrders();

        $status = $this->filters['status'];
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

            if ($status) {
                $qb->andWhere(
                   $qb->expr()->in('o.status', $status));
            }
        }

        return $qb;

    }
}
