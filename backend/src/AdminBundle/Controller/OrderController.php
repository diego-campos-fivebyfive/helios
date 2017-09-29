<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\Order;
use AppBundle\Form\Order\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_PLATFORM_COMMERCIAL')")
 *
 * @Route("orders")
 * @Breadcrumb("Orçamentos")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="index_orders")
     */
    public function orderAction(Request $request)
    {
        $manager = $this->manager('order');

        $qb = $manager->createQueryBuilder();

        $qb->where('o.parent is null')
            ->andWhere('o.status <> :status')
            ->setParameters([
                'status' => Order::STATUS_BUILDING
            ]);

        $user = $this->getUser();
        if(!$user->isPlatformAdmin() && !$user->isPlatformMaster()) {
            $member = $user->getInfo();

            $qbc = $this->manager('customer')->createQueryBuilder();

            $qbc->select('c.id')
                ->where("c.context = :context")
                ->andWhere('c.agent = :agent')
                ->setParameters([
                    'context' => Customer::CONTEXT_ACCOUNT,
                    'agent' => $member->getId()
                ]);

            $accounts = array_map('current',$qbc->getQuery()->getResult());
            if (count($accounts) == 0)
                $accounts = 0;

            $qb->andWhere(
                $qb->expr()->in('o.account',
                    $accounts
                )
            );
        }

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/orders/index.html.twig', array(
            'orders' => $pagination
        ));
    }

    /**
     * @Route("/{id}/info", name="orders_info")
     */
    public function infoAction(Request $request, Order $order)
    {
        $form = $this->createForm(OrderType::class, $order, [
            'target' => OrderType::TARGET_REVIEW,
            'action' => $this->generateUrl('orders_info', ['id' => $order->getId()]),
            'paymentMethods' => $this->getPaymentMethods(),
            'member' => $this->member()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->manager('order')->save($order);

            return $this->json();
        }

        return $this->render('admin/orders/info.html.twig', array(
            'order' => $order,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/proforma/{id}", name="index_proforma")
     */
    public function proformaAction(Order $order)
    {
        return $this->render('admin/orders/proforma.html.twig', array(
            'order' => $order
        ));
    }

    private function getPaymentMethods()
    {
        $manager = $this->manager('parameter');

        if(null == $parameter = $manager->find('payment_methods')){
            $parameter = $manager->create();
        }

        $paymentMethods = [];
        foreach ($parameter->all() as $key => $paymentMethod){
            if($paymentMethod['enabled']) {
                $paymentMethods[json_encode($paymentMethod)] = $paymentMethod['name'];
            }
        }

        return $paymentMethods;
    }

    private function findPaymentMethod()
    {

    }
}
