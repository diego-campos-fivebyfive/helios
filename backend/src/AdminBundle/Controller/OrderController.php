<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Order\FilterType;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Form\Order\OrderType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

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
        $member = $this->member();

        $form = $this->createForm(FilterType::class, null, [
            'member' => $member
        ]);

        $data = $form->handleRequest($request)->getData();

        if(!$data['agent']) $data['agent'] = $member;

        /** @var \AppBundle\Service\Order\OrderFinder $finder */
        $finder = $this->get('order_finder');

        $finder
            ->set('agent', $data['agent'])
            ->set('filter', $data)
        ;

        $qb = $finder->queryBuilder();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/orders/index.html.twig', array(
            'orders' => $pagination,
            'member' => $member,
            'form' => $form->createView()
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
     * @Route("/{id}/shipping", name="order_shipping")
     */
    public function shippingAction(Order $order)
    {
        return $this->render('admin/orders/shipping.html.twig', array(
            'order' => $order
        ));
    }

    /**
     * @Route("/element/{id}/update", name="order_element_update")
     */
    public function updateOrderElementAction(Request $request, Element $element)
    {
        $manager = $this->manager('order_element');

        try {
            $markup = $request->get('markup');

            $element->setMarkup($markup/100);

            $manager->save($element);

            $status = Response::HTTP_OK;
        } catch (\Exception $exception) {
            $status = Response::HTTP_EXPECTATION_FAILED;
        }

        return $this->json([
            'total' => $element->getOrder()->getTotal()
        ], $status);
    }

    /**
     * @return array
     */
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
