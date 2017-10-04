<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\Order;
use AppBundle\Form\Order\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_PLATFORM_AFTER_SALES')")
 *
 * @Route("orders")
 * @Breadcrumb("Orçamentos")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/", name="orders")
     */
    public function orderAction(Request $request)
    {
        $agent = $this->member();

        /** @var \AppBundle\Service\Order\OrderFinder $finder */
        $finder = $this->get('order_finder');

        $finder->set('agent', $agent);

        $pagination = $this->getPaginator()->paginate(
            $finder->query(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/orders/index.html.twig', array(
            'orders' => $pagination
        ));
    }

    /**
     * @Route("/accounts", name="accounts_list")
     */
    public function accountsAction(Request $request)
    {
        $paginator = $this->getPaginator();

        $parameters = [
            'context' => BusinessInterface::CONTEXT_ACCOUNT,
            'status' => BusinessInterface::ACTIVATED
        ];

        $manager = $this->manager('customer');

        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('a')
            ->from(Customer::class, 'a')
            ->where('a.context = :context')
            ->andWhere('a.status = :status');

        $user = $this->user();
        if(!$user->isPlatformAdmin() && !$user->isPlatformMaster()) {
            $member = $this->member();

            $qb->andWhere('a.agent = :agent');

            $parameters['agent'] = $member->getId();
        }

        $qb->setParameters($parameters);

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10
        );

        return $this->render('admin/orders/accounts.html.twig', array(
            'pagination' => $pagination
        ));
    }

    /**
     * @Route("/show/{id}", name="order_sices_show")
     */
    public function showAction(Order $order)
    {
        return $this->render('admin/orders/show.html.twig', array(
            'order' => $order
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
}
