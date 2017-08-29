<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order\Order;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("orders")
 * @Breadcrumb("Meus Pedidos")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="index_order")
     */
    public function orderAction(Request $request)
    {
        $manager = $this->manager('order');

        $qb = $manager->createQueryBuilder();
        $qb2 = $manager->getEntityManager()->createQueryBuilder();
        $qb->where(
            $qb->expr()->in('o.id',
                $qb2->select('o2')
                    ->from(Order::class, 'o2')
                    ->where('o2.parent is null')
                ->getQuery()->getDQL()
            )
        )->andWhere('o.account = :account')
        ->setParameter('account', $this->account());

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('Order.index', array(
            'orders' => $pagination
        ));
    }

    /**
     * @Route("/{id}", name="order_show")
     * @Method("get")
     */
    public function showAction(Request $request, Order $order)
    {
        return $this->render('order.show', [
            'order' => $order,
            'element' => $order->getElements()
        ]);
    }

    /**
     * @Route("/budgets/create", name="order_budget_create")
     */
    public function createBudgetAction(Request $request)
    {
        $childrens = $request->get('childrens');

        $transformer = $this->get('order_transformer');

        /** @var Order $order */
        $order = $transformer->transformFromChildrens($childrens);

        $this->get('notifier')->notify([
            'Evento' => '5021',
            'Callback' => 'order_created',
            'Id' => $order->getId()
        ]);

        return $this->json([
            'order' => [
                'id' => $order->getId()
            ]
        ]);
    }

}