<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Order\FilterType;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Form\Order\OrderType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("has_role('ROLE_PLATFORM_LOGISTIC') or has_role('ROLE_PLATFORM_EXPANSE')")
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

        $dateStatus = $data['statusAt'];
        $formatDateStatus = function($dateStatus){
            return implode('-', array_reverse(explode('/', $dateStatus)));
        };

        $dateDelivery = $data['deliveryAt'];
        $formatDateDelivery  = function($dateDelivery ){
            return implode('-', array_reverse(explode('/', $dateDelivery )));
        };


        if(is_array($data) && !array_key_exists('agent',$data) || !$data['agent'])
            $data['agent'] = $member;

        /** @var \AppBundle\Service\Order\OrderFinder $finder */
        $finder = $this->get('order_finder');

        $finder
            ->set('agent', $data['agent'])
            ->set('filter', $data)
        ;

        $qb = $finder->queryBuilder();

        if ($dateStatus)
            $this->filterDateStatusAt($qb, $dateStatus, $formatDateStatus);

        if ($dateDelivery)
            $this->filterDateDeliveryAt($qb, $dateDelivery, $formatDateDelivery);

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
     * @Route("/{id}/invoice", name="invoice_number")
     */
    public function invoiceNumberAction(Request $request, Order $order)
    {
        $invoice = $request->request->get('invoice_number');

        $order->setInvoiceNumber($invoice);

        $this->manager('order')->save($order);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/deliveryAt", name="delivery_at_order")
     */
    public function deliveryAtAction(Request $request, Order $order)
    {
        $date = $request->get('delivery');

        $formatDate = function($date){
            return implode('-', array_reverse(explode('/', $date)));
        };

        $deliveryAt = new \DateTime($formatDate($date));

        $order->setDeliveryAt($deliveryAt);

        $this->manager('order')->save($order);

        return $this->json([
            'deliveryAt' => $order->getDeliveryAt()
        ]);
    }

    /**
     * @Route("/{filename}/import", name="import_csv")
     */
    public function importAction(Request $request, $filename)
    {
        $kernel = $this->get('kernel')->getCacheDir();

        if (!$filename) {
            /** @var File $file */
            $file = $request->files->get('file');

            if (!$file instanceof UploadedFile) {
                return $this->render('admin/orders/upload_csv.html.twig');
            }

            $filename = md5(uniqid(time())) . '.csv';

            $file->move($kernel, $filename);

            return $this->json(['name' => $filename]);
        } else {
            $file = $kernel . '/' . $filename;
            $content = file_get_contents($file);
            $data = explode("\n", $content);
            array_shift($data);

            $mapping = [];

            foreach ($data as $key => $line) {

                $info = explode(';', $line);

                if(array_key_exists(7, $info)) {
                    $reference = $info[4];

                    if (is_numeric($reference)) {

                        $nf = $info[7];

                        $mapping[$reference] = $nf;
                    }
                }
            }

            $manager = $this->manager('order');

            $qb = $manager->createQueryBuilder();

            $orders = [];

            if ($mapping)
                $orders = $qb->where($qb->expr()->in('o.reference',array_keys($mapping)))->getQuery()->getResult();

            $importations = 0;

            /** @var OrderInterface $order */
            foreach ($orders as $order) {
                $order->setInvoiceNumber($mapping[$order->getReference()]);
                $manager->save($order);
                $importations++;
            }

            return $this->json(['importations' => $importations]);
        }
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

    /**
     * @param $qb
     * @param $dateStatus
     * @param $formatDateStatus
     * @return mixed
     */
    private function filterDateStatusAt($qb, $dateStatus, $formatDateStatus)
    {
        $dateStatus = explode(' - ',$dateStatus);
        $startAt = new \DateTime($formatDateStatus($dateStatus[0]));
        $endAt = new \DateTime($formatDateStatus($dateStatus[1]));

        $qb->andWhere('o.statusAt >= :startAt');
        $qb->andWhere('o.statusAt <= :endAt');

        $qb->setParameter('startAt', $startAt->format('Y-m-d 00:00:00'));
        $qb->setParameter('endAt', $endAt->format('Y-m-d 23:59:59'));

        return $qb;
    }

    /**
     * @param $qb
     * @param $dateDelivery
     * @param $formatDateDelivery
     * @return mixed
     */
    private function filterDateDeliveryAt($qb, $dateDelivery, $formatDateDelivery)
    {
        $dateDelivery = explode(' - ',$dateDelivery);
        $startAt = new \DateTime($formatDateDelivery($dateDelivery[0]));
        $endAt = new \DateTime($formatDateDelivery($dateDelivery[1]));

        $qb->andWhere('o.deliveryAt >= :startAt');
        $qb->andWhere('o.deliveryAt <= :endAt');

        $qb->setParameter('startAt', $startAt->format('Y-m-d 00:00:00'));
        $qb->setParameter('endAt', $endAt->format('Y-m-d 23:59:59'));

        return $qb;
    }
}
