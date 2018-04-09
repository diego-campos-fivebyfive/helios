<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Order\FilterType;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\Maker;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Order\Element;
use AppBundle\Configuration\Brazil;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Form\Order\OrderType;
use AppBundle\Manager\OrderElementManager;
use AppBundle\Service\Order\OrderExporter;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

        $expanseStates = [];
        $filteredStates = [];
        $filteredStatus = [];
        $filteredSubStatus = [];
        $filteredComponents = [];

        $qb =
            $this->filterOrders(
                $request,
                $data,
                $expanseStates,
                $filteredStates,
                $filteredStatus,
                $filteredSubStatus,
                $filteredComponents
            );

        $hasFilter = $this->checkFilter($data);

        if ($filteredStates || $filteredStatus || $filteredSubStatus || $filteredComponents) {
            $hasFilter = true;
        }

        $totals = $this->getFiltersTotals(clone $qb);

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/orders/index.html.twig', array(
            'orders' => $pagination,
            'member' => $member,
            'form' => $form->createView(),
            'totals' => $totals,
            'states' => $this->resolveFilters($this->getStates($expanseStates), $filteredStates),
            'statusList' => $this->resolveFilters(Order::getStatusNames(), $filteredStatus),
            'subStatusList' => $this->resolveSubStatus(Order::getSubStatusNames(), $filteredSubStatus),
            'componentsList' => $this->resolveFilters($this->getComponentsList(), $filteredComponents),
            'hasFilter' => $hasFilter
        ));
    }

    /**
     * @Route("/{id}/elementMetadata", name="element_metadata")
     *
     * @Method("post")
     */
    public function ElementMetadataAction(Request $request, Element $element)
    {
        $metadata = $request->request->all();

        array_map(function ($key, $value) use ($element) {
            if ($value) {
                if ($value == "true" || $value == "false") {
                    $value = $value == "true" ? true : false;
                }
                $element->addMetadata($key, $value);
            } else {
                $element->removeMetadata($key);
            }
        }, array_keys($metadata), $metadata);

        $manager = $this->manager('order_element');

        $manager->save($element);

        return $this->json([]);
    }

    /**
     * @Route("/{mode}/export", name="export_list")
     */
    public function exportAction(Request $request, $mode)
    {
        $member = $this->member();

        $form = $this->createForm(FilterType::class, null, [
            'member' => $member
        ]);

        $data = $form->handleRequest($request)->getData();

        $qb = $this->filterOrders($request, $data);

        $results = $qb->getQuery()->getResult();

        /** @var OrderExporter $orderExporter */
        $orderExporter = $this->container->get('order_exporter');

        $path = $orderExporter->export($results, $mode);

        $header = ('spreadsheet') ? ResponseHeaderBag::DISPOSITION_ATTACHMENT : ResponseHeaderBag::DISPOSITION_INLINE;

        $file = new BinaryFileResponse($path, Response::HTTP_OK, [], true, $header);

        return $file;
    }

    /**
     * @Route("/accounts", name="accounts_list")
     */
    public function accountsAction(Request $request)
    {
        $paginator = $this->getPaginator();

        $parameters = [
            'context' => BusinessInterface::CONTEXT_ACCOUNT,
            'status' => AccountInterface::ACTIVATED
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
     * @Route("/{id}/shippingValue", name="shipping_value")
     */
    public function shippingValueAction(Request $request, Order $order)
    {
        $shipping = $request->get('shipping');
        $order->setShipping(str_ireplace(['.',','], ['','.'],$shipping));

        $this->manager('order')->save($order);

        return $this->json([
            'value' => $order->getShipping(),
            'total' => $order->getTotal()
        ]);
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
     * @Route("/{id}/invoice", name="invoice_number")
     */
    public function invoiceNumberAction(Request $request, Order $order)
    {
        $invoices = explode(',', $request->request->get('invoice_number'));

        $order->setInvoices($invoices);

        $this->manager('order')->save($order);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/billed", name="billed_at")
     */
    public function billedAtAction(Request $request, Order $order)
    {
        $dateBilled = $request->get('billed_at');
        $formatDate = function($dateBilled){
            return implode('-', array_reverse(explode('/', $dateBilled)));
        };

        $billedAt = new \DateTime($formatDate($dateBilled));

        $order->setBilledAt($billedAt);

        $this->manager('order')->save($order);

        return $this->json([
            'billedAt' => $order->getBilledAt()
        ]);
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
     * @Route("/{id}/expireAt", name="expire_at_order")
     */
    public function expireAtAction(Request $request, Order $order)
    {
        $date = $request->get('expire');

        $formatDate = function($date){
            return implode('-', array_reverse(explode('/', $date)));
        };

        $expireAt = new \DateTime($formatDate($date));

        $order->setExpireAt($expireAt);

        $this->manager('order')->save($order);

        return $this->json([
            'expireAt' => $order->getExpireAt()
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

            if ($mapping) {
                $orders = $qb->where($qb->expr()->in('o.reference',array_keys($mapping)))->getQuery()->getResult();
            }

            $importations = 0;

            $dateBilled = $request->get('billed');
            $formatDate = function($dateBilled){
                return implode('-', array_reverse(explode('/', $dateBilled)));
            };

            $billedAt = new \DateTime($formatDate($dateBilled));

            /** @var OrderInterface $order */
            foreach ($orders as $order) {
                $order->setBilledAt($billedAt);
                $order->setInvoiceNumber($mapping[$order->getReference()]);
                $manager->save($order);
                $importations++;
            }

            return $this->json(['importations' => $importations]);
        }
    }

    /**
     * @param $qb
     * @return mixed
     */
    private function getFiltersTotals($qb)
    {
        $qb->select('DISTINCT(o.id)');

        $ids = array_map('current', ($qb->getQuery()->getResult()));
        $ids = $ids ? $ids : [0];

        $qb2 = $this->manager('order')->createQueryBuilder();

        $qb2
            ->select('sum(o.total) as total, sum(o.power) as power')
            ->where($qb2->expr()->in('o.id', $ids));

        return current($qb2->getQuery()->getResult());
    }

    /**
     * @param $request
     * @param $data
     * @param array $expanseStates
     * @param array $filteredStates
     * @param array $filteredStatus
     * @param array $filteredComponents
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function filterOrders($request, $data, &$expanseStates = [], &$filteredStates = [], &$filteredStatus = [], &$filteredSubStatus = [], &$filteredComponents = [])
    {
        $optionVal = $data['optionsVal'];
        $valueMin = $data['valueMin'] ? str_replace(',', '.', $data['valueMin']) : null;
        $valueMax = $data['valueMax'] ? str_replace(',', '.', $data['valueMax']) : null;

        $dateAt = $data['dateAt'];
        $optionDate = $data['optionsAt'];

        $formatDateAt = function($dateAt){
            return implode('-', array_reverse(explode('/', $dateAt)));
        };

        if(is_array($data) && !array_key_exists('agent',$data) || !$data['agent']) {
            $data['agent'] = $this->member();
        }

        /** @var \AppBundle\Service\Order\OrderFinder $finder */
        $finder = $this->get('order_finder');

        $finder
            ->set('agent', $data['agent'])
            ->set('filter', $data)
        ;

        $qb = $finder->queryBuilder();

        if ($dateAt) {
            $this->filterDateAt($qb, $optionDate, $dateAt, $formatDateAt);
        }

        if ($valueMin) {
            $qb->andWhere('o.'.$optionVal.' >= :valMin');

            $qb->setParameter('valMin', $valueMin);
        }

        if ($valueMax) {
            $qb->andWhere('o.'.$optionVal.' <= :valMax');

            $qb->setParameter('valMax', $valueMax);
        }

        if(-1 != $states = $request->get('states')){
            $filteredStates = array_filter(explode(',', $states));
            if (!empty($filteredStates)) {
                $qb->andWhere($qb->expr()->in('o.state', $filteredStates));
            };
        }

        if ($request->get('status') ||  $request->get('substatus')) {
            $status = explode(',', $request->get('status'));
            $filteredStatus = array_filter($status, 'strlen');

            $subStatus = explode(',', $request->get('substatus'));
            $filteredSubStatus = array_filter($subStatus, 'strlen');

            $values = [];
            $values2 = [];

            foreach ($filteredSubStatus as $value) {
                array_push($values, substr($value, -1));
                array_push($values2, substr($value, -2, 1));
            }

            if (!empty($filteredStatus) && empty($filteredSubStatus)) {
                $qb->andWhere($qb->expr()->in('o.status', $filteredStatus));
            } else if (!empty($filteredSubStatus) && empty($filteredStatus)) {
                $qb->andWhere($qb->expr()->in('o.status', $values2));
                $qb->andWhere($qb->expr()->in('o.subStatus', $values));
            } else {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->in('o.status', $filteredStatus),
                        $qb->expr()->andX(
                            $qb->expr()->in('o.status', $values2),
                            $qb->expr()->in('o.subStatus', $values)
                        )
                    )
                );
            }
        }

        if (-1 != $components = $request->get('components')) {
            $components = explode(',', $components);
            $filteredComponents = array_filter($components, 'strlen');
            if (!empty($filteredComponents)) {
                $qb->andWhere($qb->expr()->in('e.code', $filteredComponents));
            }
        }

        if ($this->member()->isPlatformExpanse()) {
            $expanseStates = $this->member()->getAttributes()['states'];
            $qb->andWhere($qb->expr()->in('o.state', $expanseStates));
        }


        return $qb;
    }

    /**
     * @return array
     */
    private function getComponentsList()
    {
        $families = Maker::getContextList();

        $componentsList = [];

        foreach ($families as $family) {
            $qb = $this->manager($family)->createQueryBuilder();

            $components = $qb->select($qb->getAllAliases()[0])->getQuery()->getResult();

            foreach ($components as $component) {
                $componentsList[$component->getCode()] = $component->getDescription();
            }
        }

        return $componentsList;
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
     * @param $option
     * @param $dateAt
     * @param $formatDateAt
     * @return mixed
     */
    private function filterDateAt($qb, $option, $dateAt, $formatDateAt)
    {
        $dateAt = explode(' - ',$dateAt);
        $startAt = new \DateTime($formatDateAt($dateAt[0]));
        $endAt = new \DateTime($formatDateAt($dateAt[1]));

        $qb->andWhere('o.'.$option.' >= :startAt');
        $qb->andWhere('o.'.$option.' <= :endAt');

        $qb->setParameter('startAt', $startAt->format('Y-m-d 00:00:00'));
        $qb->setParameter('endAt', $endAt->format('Y-m-d 23:59:59'));

        return $qb;
    }

    /**
     * @return array
     */
    private function getStates($filterStates)
    {
        $allStates = Brazil::states();

        if (!$this->member()->isPlatformExpanse()) {
            return $allStates;
        }

        $states = [];
        foreach ($filterStates as $state) {
            $states[$state] = $allStates[$state];
        }

        asort($states);
        return $states;
    }

    /**
     * @param array $options
     * @param array $selected
     * @return array
     */
    private function resolveFilters(array $options, array $selected)
    {
        $finalOptions = [];

        foreach ($options as $key => $option) {
            $finalOptions[$key] = [
                'name' => $option,
                'checked' => in_array($key, $selected)
            ];
        }

        return $finalOptions;
    }

    /**
     * @param array $options
     * @param array $selected
     * @return array
     */
    private function resolveSubStatus(array $subStatus, array $selected)
    {
        $finalOptions = [];

        foreach ($subStatus as $key1 => $options) {
            foreach ($options as $key2 => $option) {

                $finalOptions[$key1][] = [
                    'name' => $option,
                    'checked' => in_array($key1 . $key2, $selected)
                ];
            }
        }

        return $finalOptions;
    }

    /**
     * @param $data
     * @return bool
     */
    private function checkFilter($data) {
        if ($data['status'] || $data['like'] || $data['agent'] ||
            $data['dateAt'] || $data['valueMin'] || $data['valueMax']) {
            return true;
        }

        return false;
    }
}
