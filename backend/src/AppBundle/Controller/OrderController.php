<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Misc\Coupon;
use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Message;
use AppBundle\Entity\Order\MessageInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderAdditive;
use AppBundle\Entity\Order\OrderAdditiveInterface;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\TimelineInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Form\Order\FilterType;
use AppBundle\Service\Order\OrderCoupon;
use AppBundle\Service\Order\StatusMapping;
use AppBundle\Service\ProjectGenerator\ShippingRuler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


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
        $form = $this->createForm(FilterType::class);

        $filter = $form->handleRequest($request)->getData();

        if (is_array($filter) && array_key_exists('like', $filter)) {
            $filter['likes'] = ['o.reference'];
        }

        /** @var \AppBundle\Service\Order\OrderFinder $finder */
        $finder = $this->get('order_finder');

        $finder
            ->set('member', $this->member())
            ->set('filter', $filter);

        $qb = $finder->queryBuilder();

        $optionVal = $filter['optionsVal'];
        $valueMin = $filter['valueMin'] ? str_replace(',', '.', $filter['valueMin']) : null;
        $valueMax = $filter['valueMax'] ? str_replace(',', '.', $filter['valueMax']) : null;

        if ($valueMin) {
            $qb->andWhere('o.' . $optionVal . ' >= :valMin');

            $qb->setParameter('valMin', $valueMin);
        }

        if ($valueMax) {
            $qb->andWhere('o.' . $optionVal . ' <= :valMax');

            $qb->setParameter('valMax', $valueMax);
        }

        $statusAt = $filter['statusAt'];

        if ($statusAt) {
            $formatDateAt = function ($dateAt) {
                return implode('-', array_reverse(explode('/', $dateAt)));
            };

            $statusAt = explode(' - ', $statusAt);
            $startAt = new \DateTime($formatDateAt($statusAt[0]));
            $endAt = new \DateTime($formatDateAt($statusAt[1]));

            $qb->andWhere('o.statusAt >= :startAt');
            $qb->andWhere('o.statusAt <= :endAt');

            $qb->setParameter('startAt', $startAt->format('Y-m-d 00:00:00'));
            $qb->setParameter('endAt', $endAt->format('Y-m-d 23:59:59'));
        }

        if (-1 != $status = $request->get('status')) {
            $status = explode(',', $status);
            $arrayStatus = array_filter($status, 'strlen');
            if (!empty($arrayStatus)) {
                $qb->andWhere($qb->expr()->in('o.status', $arrayStatus));
            }
        }

        $qbTotals = clone $qb;
        $qbTotals->resetDQLPart('join');
        $qbTotals->select('sum(o.total) as total, sum(o.power) as power');
        $totals = current($qbTotals->getQuery()->getResult());

        $getStates = function ($statusList, $arrayStatus) {
            $finalOptions = [];
            foreach ($statusList as $key => $status) {
                $finalOptions[$key] = [
                    'name' => $status,
                    'checked' => in_array($key, $arrayStatus)
                ];
            }

            return $finalOptions;
        };

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('order.index', array(
            'orders' => $pagination,
            'form' => $form->createView(),
            'totals' => $totals,
            'statusList' => $getStates(Order::getStatusNames(), $arrayStatus)
        ));
    }

    /**
     * @Route("/{id}/show", name="order_show")
     */
    public function showAction(Order $order)
    {
        $expired = $this->checkMemorial()->getPublishedAt() > $order->getCreatedAt();

        $this->denyAccessUnlessGranted('view', $order);

        $filesNfe = array_key_exists('nfe', $order->getFiles()) ? $order->getFiles()['nfe'] : [];

        $files = [];
        foreach ($filesNfe as $file) {
            $invoice = substr($file,25,9);

            $files[] = [
                'name'=> $invoice.'.'.explode('.',$file)[1],
                'file' => $file
            ];
        }

        return $this->render('admin/orders/show.html.twig', array(
            'order' => $order,
            'expired' => $expired,
            'timeline' => $this->get('order_timeline')->load($order),
            'files' => $files,
            'buttons' => $this->getButtons($order, $expired)
        ));
    }

    /**
     * @Route("/generate_coupon", name="generate_coupon_from_order")
     */
    public function generateCouponAction(Request $request)
    {
        $orderId = $request->get('order');

        $order = $this->manager('order')->find($orderId);
        $step = $request->get('step');

        /** @var OrderCoupon $orderCoupon */
        $orderCoupon = $this->container->get('order_coupon');

        $coupon = $orderCoupon->generateCoupon($order, $step);

        if (!$coupon) {
            return $this->json([
                'message' => 'Coupon could not be generated'
            ],
                Response::HTTP_BAD_REQUEST);
        }

        return $this->json([]);
    }

    /**
     * @Route("/{id}/coupon", name="coupon_create")
     */
    public function couponAction(Request $request, Order $order)
    {
        /** @var OrderCoupon $orderCoupon */
        $orderCoupon = $this->container->get('order_coupon');

        $orderCoupon->checkCouponAssociation($order);

        $options = $orderCoupon->generateOptions($order);

        $isShow = $request->get('isShow');

        return $this->render('admin/orders/coupon_form.html.twig', array(
            'order' => $order,
            'options' => $options,
            'isShow' => $isShow
        ));
    }

    /**
     * @Route("/{order}/{coupon}/associate_coupon", name="associate_coupon")
     */
    public function associateCouponAction(Order $order, Coupon $coupon)
    {
        /** @var OrderCoupon $orderCoupon */
        $orderCoupon = $this->container->get("order_coupon");

        $associated = $orderCoupon->associateCoupon($order, $coupon);

        $status = $associated ? Response::HTTP_OK : Response::HTTP_UNAUTHORIZED;

        return $this->json([], $status);
    }

    /**
     * @Route("/{id}/status", name="order_status")
     * @Method("post")
     */
    public function statusAction(Request $request, Order $order)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        $status = (int) $request->get('status');
        $subStatus = $request->get('subStatus');

        /** @var \AppBundle\Service\Order\StatusChanger $changer */
        $changer = $this->get('order_status_changer');

        if($changer->accept($order, $status, $this->user()) or $this->user()->isPlatform()) {

            if ($subStatus == "undefined") {
                $changer->change($order, $status);
            } else {
                $changer->change($order, $status, $subStatus);
            }

            return $this->json();
        }

        return $this->json([
            'error' => 'O status solicitado não pode ser definido.'
        ], Response::HTTP_BAD_REQUEST);
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
     * @Route("/budgets/create", name="order_budget_create")
     */
    public function createBudgetAction(Request $request)
    {
        $manager = $this->manager('order');

        /** @var Order $order */
        $order = $manager->find($request->get('orderId'));

        $this->get('order_status_changer')->change($order, Order::STATUS_PENDING);

        return $this->json([
            'order' => [
                'id' => $order->getId()
            ]
        ]);
    }

    /**
     * @Route("/element/{id}/update", name="order_element_update")
     * @Method("post")
     */
    public function updateOrderElementAction(Request $request, Element $element)
    {
        foreach ($request->request->get('order') as $field => $value){
            $setter = 'set' . ucfirst($field);
            $element->$setter($value);
        }

        $this->manager('order_element')->save($element);

        $this->finishElement($element);

        return $this->json([
            'total' => $element->getOrder()->getTotal(),
            'power' => $element->getOrder()->getPower(),
            'description' => $element->getOrder()->getDescription()
        ]);
    }

    /**
     * @Route("/{id}/shipping_info", name="order_shipping_info")
     */
    public function shippingInfoAction(Request $request, Order $order)
    {
        if(null != $deliveryAddress = $request->request->get('deliveryAddress')){
            $order->setDeliveryAddress($deliveryAddress);
            $this->manager('order')->save($order);
        }

        return $this->render('order.shipping_info', array(
            'order' => $order
        ));
    }

    /**
     * @Route("/{id}/componentsOutOfStock", name="components_out_of_stock")
     */
    public function componentsOutOfStockAction(Order $order)
    {
        $stockChecker = $this->container->get('order_stock_checker');

        $componentsOutOfStock = $stockChecker->checkOutOfStock($order);

        return $this->render('order.components_out_of_stock', array(
           'componentsOutOfStock' => $componentsOutOfStock
        ));
    }

    /**
     * @Route("/{id}/upload", name="order_upload")
     */
    public function uploadAction(Order $order, Request $request)
    {
        $file = $request->files->get('file');

        if (!$file instanceof UploadedFile) {
            return $this->render('order.upload', [ 'order' => $order ]);
        }

        $format = 'filePayment_%s_%s.%s';
        $date = (new \DateTime())->format('Ymd-His');
        $extention = $file->getClientOriginalExtension();

        $filename = sprintf($format, $order->getId(), $date, $extention);

        $options = [
            'filename' => $filename,
            'root' => 'order',
            'type' => 'payment',
            'access' => 'private'
        ];

        $this->container->get('app_storage')->push($options, $file);

        $location = $this->container->get('app_storage')->location($options);
        $path = str_replace($filename, '', $location);
        $file->move($path, $filename);

        $order->addFile('payment', $filename);

        $this->manager('order')->save($order);

        $this->get('order_timeline')->create($order, TimelineInterface::TAG_FILE_PAYMENT);

        return $this->json([ 'name' => $filename ]);
    }

    /**
     * @Route("/{id}/files/{type}", name="order_files")
     */
    public function getFilesAction(Order $order, $type)
    {
        return $this->render('order.files', [
            'order' => $order,
            'type' => $type,
            'isPlatform' => $this->user()->isPlatform()
        ]);
    }

    /**
     * @Route("/{id}/files/delete/{type}/{file}", name="order_delete_file")
     * @Method("delete")
     */
    public function deleteFileAction(Order $order, $type, $file)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        $order->removeFile($type, $file);

        $this->manager('order')->save($order);

        $options = [
            'filename' => $file,
            'root' => 'order',
            'type' => 'payment',
            'access' => 'private'
        ];

        $this->get('app_storage')->remove($options);

        return $this->json([]);
    }

    /**
     * @Route("/{order}/additive-list/", name="additive_lists")
     */
    public function additivesListsAction(Order $order)
    {
        $synchronizer = $this->get('additive_synchronizer');

        $insurances = $synchronizer->normalizeInsurances($order, $this->member());

        return $this->render('admin/orders/insurances_list.html.twig', [
            'additives' => $insurances,
            'order' => $order
        ]);
    }

    /**
     * @Route("/{id}/file/{type}", name="order_file")
     */
    public function fileAction(Request $request, Order $order, $type)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        $method = 'get' . ucfirst($type);

        if ('nfe' != $type && !method_exists($order, $method)) {
            $message = 'The class %s does not have %s file';
            throw $this->createNotFoundException(sprintf($message, get_class($order), $type));
        }

        if('nfe' != $type) {
            $filename = $request->get('file') ? $request->get('file') : $order->$method();
        } else {
            $filename = $request->query->get('file');
        }

        if (!$filename) {
            $message = 'File %s not found';
            throw $this->createNotFoundException(sprintf($message, $type));
        }

        $header = ($type == 'fileExtract' || $type == 'nfe') ? ResponseHeaderBag::DISPOSITION_ATTACHMENT : ResponseHeaderBag::DISPOSITION_INLINE;

        $root = 'order';

        switch ($type){
            case 'proforma':
                $dir = 'proforma';
                break;
            case 'fileExtract':
                $dir = 'order';
                break;
            case 'filePayment':
                $dir = 'payment';
                break;
            case 'nfe':
                $dir = 'danfe';
                $root = 'fiscal';
                break;
        }

        $options = [
            'filename' => $filename,
            'root' => $root,
            'type' => $dir,
            'access' => 'private'
        ];

        $file = $this->container->get('app_storage')->display($options);

        if (is_file($file)) {
            return new BinaryFileResponse($file, Response::HTTP_OK, [], true, $header);
        }
    }

    /**
     * @Route("/{id}/generator", name="proforma_pdf_generator")
     */
    public function generatorProformaAction(Order $order)
    {
        $id = $order->getId();
        $date = (new \DateTime())->format('Ymd-His');
        $filename = sprintf('proforma_%s_%s_.pdf', $order->getId(), $date);

        $absoluteUrl = UrlGeneratorInterface::ABSOLUTE_URL;
        $snappyUrl = $this->generateUrl('proforma_pdf', ['id' => $id], $absoluteUrl);

        $options = array(
            'id' => $id,
            'root' => 'order',
            'type' => 'proforma',
            'filename' => $filename,
            'access' => 'private',
            'snappy' => $snappyUrl
        );

        $file = $this->get('app_storage')->location($options);

        $this->get('app_generator')->pdf($options, $file);

        if (!file_exists($file)) {
            $message = "Could not generate proforma PDF.";
            return $this->json([ 'error' => $message ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->get('app_storage')->push($options, $file);

        $order->setProforma($filename);

        $this->manager('order')->save($order);

        return $this->json([ 'filename' => $filename ], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}/delete", name="order_delete")
     * @Method("delete")
     */
    public function deleteAction(Order $order)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        if(in_array($order, [$order->isBuilding(), $order->isRejected()])){
            $this->manager('order')->delete($order);
            return $this->json([]);
        }

        return $this->json([
            'error' => 'Somente orçamentos em edição podem ser excluídos'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{id}/message", name="order_message")
     */
    public function orderMessageAction(Request $request, Order $order)
    {
        $contentMessage = $request->get('message') ? $request->get('message') : $request->get('note');

        if ($request->isMethod('POST') && $contentMessage) {

            $messageManager = $this->manager('order_message');

            /** @var MessageInterface $message */
            $message = $messageManager->create();

            $message
                ->setOrder($order)
                ->setAuthor($this->member())
                ->setContent($contentMessage)
                ->setRestricted(false);

            if ($request->get('restricted')) {
                $message->setRestricted(true);

                $toUsers = $request->get('users') ? $request->get('users') : [];

                $toRoles = $request->get('roles') ? $request->get('roles') : [];

                $members = $this->account()->getMembers();

                /** @var MemberInterface $member */
                foreach ($members as $member) {
                    $roleAccepted = in_array($member->getUser()->getRole(), $toRoles);

                    $added = in_array($member->getId(), $toUsers);

                    if ($roleAccepted && !$added) {
                        $toUsers[] = $member->getId();
                    }
                }

                $toUsers = array_map(function ($user) {
                    return '"'.$user.'"';
                }, $toUsers);

                $message->setTo($toUsers);
                $message->setRead($toUsers);
            }

            $messageManager->save($message);
        }

        $messages = $order->getMessages();

        return $this->render('orders/messages/messages.html.twig', [
            'messages' => $messages->getValues(),
            'order' => $order
        ]);
    }

    /**
     * @Route("/{id}/email", name="message_email")
     */
    public function messageEmailAction(Order $order)
    {
        $this->getMailer()->sendOrderMessage($order);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/message/{message}/delete", name="order_message_delete")
     *
     * @Method("delete")
     */
    public function messageDeleteAction(Order $order, Message $message)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        $this->manager('order_message')->delete($message);

        return $this->json([]);
    }

    /**
     * @Route("/{id}/show-cloner/", name="show_order_cloner")
     */
    public function orderShowClonerAction(Request $request, Order $order)
    {
        $source = $request->get('source');

        return $this->render('admin/orders/quantity_cloner.html.twig', [
            'order' => $order,
            'source' => $source
        ]);
    }

    /**
     * @Route("/{id}/cloner/", name="sub_order_copy")
     */
    public function orderClonerAction(Request $request, Order $order)
    {
        $quantity = $request->get('quantity');

        $cloner = $this->getCloner();

        $cloner->cloneOrder($order, (int)$quantity);

        return $this->json([]);
    }

    /**
     * @param Order $order
     * @param Additive $additive
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/{order}/{additive}/additive/", name="create_order_additive")
     */
    public function createOrderAdditiveAction(Order $order, Additive $additive)
    {
        $manager = $this->manager('order_additive');

        /** @var OrderAdditiveInterface $orderAdditive */
        $orderAdditive = $manager->create();

        $orderAdditive->setAdditive($additive);
        $orderAdditive->setOrder($order);

        $manager->save($orderAdditive);

        return $this->json([
                'total' => $order->getTotal()
        ]);
    }

    /**
     * @param OrderAdditive $orderAdditive
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/additive/{orderAdditive}/delete/", name="delete_order_additive")
     */
    public function deleteOrderAdditiveAction(OrderAdditive $orderAdditive)
    {
        $order = $orderAdditive->getOrder();
        $manager = $this->manager('order_additive');

        $manager->delete($orderAdditive);

        return $this->json([
                'total' => $order->getTotal()
        ]);
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getButtons(OrderInterface $order, $expired = false)
    {
        $userType = $this->user()->getType();

        $parameters = [
            'sourceStatus' => $order->getStatus(),
            'sourceSubStatus' => $order->getSubStatus(),
            'type' => $userType,
            'previous' => [$order->getPreviousStatus(), $order->getPreviousSubStatus()]
        ];

        if ($userType = UserInterface::TYPE_PLATFORM) {
            $parameters['role'] = $this->user()->getRole();
        }

        $actions = StatusMapping::getPossibilities($parameters, true);

        return array_filter($actions, function ($action) use ($order, $expired) {
            if ($action['type'] === UserInterface::TYPE_ACCOUNT &&
                $order->getStatus() === OrderInterface::STATUS_VALIDATED &&
                $expired &&
                $action['status'] === OrderInterface::STATUS_APPROVED ||
                ($action['attributes'][StatusMapping::SPECIAL_VIEW])) {
                return false;
            }
            return true;
        });
    }

    /**
     * @param Order $order
     */
    private function sendOrderEmail(Order $order)
    {
        $this->get('order_mailer')->sendOrderMessage($order);
    }

    /**
     * @return string
     */
    private function getUploadDir($subDir = null)
    {
        $dir = $this->get('kernel')->getRootDir() . '/../../.uploads/order/';

        if($subDir){
            $dir .= $subDir . '/';
        }

        return $dir;
    }

    private function finishElement(Element $element)
    {
        $order = $element->getOrder();

        $this->get('order_precifier')->precify($order);

        $parent = $order->getParent();

        $this->calculateShipping($parent);
    }

    /**
     * @param Order $order
     * @param array $rule
     */
    private function calculateShipping(Order $order, array $rule = [])
    {
        if (empty($rule)) {
            $rule = $order->getShippingRules();
            $rule['price'] = $order->getSubTotal();
        }

        ShippingRuler::apply($rule);

        $order->setShippingRules($rule);

        $this->manager('order')->save($order);
    }

    /**
     * @return \AppBundle\Service\Order\OrderStock|object
     */
    private function getStock()
    {
        return $this->get('order_stock');
    }

    /**
     * @return \AppBundle\Service\Mailer|object
     */
    private function getMailer()
    {
        return $this->get('app_mailer');
    }

    /**
     * @return \AppBundle\Service\Order\OrderExporter|object
     */
    private function getExporter()
    {
        return $this->get('order_exporter');
    }

    /**
     * @return \AppBundle\Service\Order\OrderCloner
     */
    private function getCloner()
    {
        return $this->get('order_cloner');
    }

    /**
     * @return \AppBundle\Entity\Pricing\MemorialInterface
     */
    private function checkMemorial()
    {
        $memorial = $this->container->get('memorial_loader')->load();

        if(!$memorial){
            throw new \InvalidArgumentException('Memorial not loaded');
        }

        return $memorial;
    }
}
