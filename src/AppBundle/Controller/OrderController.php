<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Order\Order;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("orders")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="post_order")
     */
    public function orderAction()
    {
        /** @var Customer $accounts */
        $accounts = $this->manager('customer')->find(19);
        $manager = $this->manager('order');
        $projects = [306];

        /** @var Order $order */
        $order = $manager->create();
        $order
            ->setStatus(0)
            ->setAccount($accounts);

        foreach ($projects as $id) {
            /** @var Project $project */
            $project = $this->manager('project')->find($id);
            $order->addProject($project);
        }

        $manager->save($order);

        $this->get('notifier')->notify([
            'callback' => 'order_created',
            'body' => ['id' => $order->getId()]
        ]);
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

        return $this->json([
            'order' => [
                'id' => $order->getId()
            ]
        ]);
    }

}
