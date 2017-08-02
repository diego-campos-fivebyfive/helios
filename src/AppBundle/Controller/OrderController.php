<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Order\Order;

/**
 * @Route("order")
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
        $projects = [317,318];

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
}
