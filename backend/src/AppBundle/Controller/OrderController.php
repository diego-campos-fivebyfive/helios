<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order\Element;
use AppBundle\Entity\Order\Order;
use AppBundle\Form\Order\FilterType;
use AppBundle\Service\Pricing\Insurance;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
        $form = $this->createForm(FilterType::class);

        $filter = $form->handleRequest($request)->getData();

        /** @var \AppBundle\Service\Order\OrderFinder $finder */
        $finder = $this->get('order_finder');

        $finder
            ->set('member', $this->member())
            ->set('filter', $filter)
        ;

        $pagination = $this->getPaginator()->paginate(
            $finder->query(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('order.index', array(
            'orders' => $pagination,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/show", name="order_show")
     */
    public function showAction(Order $order)
    {
        $this->denyAccessUnlessGranted('view', $order);

        return $this->render('admin/orders/show.html.twig', array(
            'order' => $order
        ));
    }

    /**
     * @Route("/{id}/status", name="order_status")
     * @Method("post")
     */
    public function statusAction(Request $request, Order $order)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        $status = (int) $request->get('status');

        $manipulator = $this->get('order_manipulator');

        if($manipulator->acceptStatus($order, $status, $this->user())){

            $order->setStatus($status);

            $this->manager('order')->save($order);

            if ($order->isApproved()){
                $this->generatorProformaAction($order);
            }

            $this->sendOrderEmail($order);

            return $this->json();
        }

        return $this->json([
            'error' => 'O status solicitado não pode ser definido.'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/budgets/create", name="order_budget_create")
     */
    public function createBudgetAction(Request $request)
    {
        $manager = $this->manager('order');

        /** @var Order $order */
        $order = $manager->find($request->get('orderId'));

        $order->setSendAt(new \DateTime('now'));
        $order->setMetadata($order->getChildrens()->first()->getMetadata());
        $order->setStatus(Order::STATUS_PENDING);
        $manager->save($order);

        $this->sendOrderEmail($order);

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

        return $this->json([
            'total' => $element->getOrder()->getTotal()
        ]);
    }

    /**
     * @Route("/{id}/shipping", name="order_shipping_info")
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
     * @Route("/{id}/insure", name="order_insure")
     * @Method("post")
     */
    public function insureAction(Order $order, Request $request)
    {
        $insure = $order->getLevel() == 'promotional' ? true : $request->get('insure');

        Insurance::apply($order, (bool) $insure);

        $this->manager('order')->save($order);

        return $this->json([
            'order' => [
                'id' => $order->getId(),
                'insurance' => $order->getInsurance(),
                'total' => $order->getTotal()
            ]
        ]);
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

        $order->setFilePayment($filename);

        $this->manager('order')->save($order);

        return $this->json([ 'name' => $filename ]);
    }

    /**
     * @Route("/{id}/file/{type}", name="order_file")
     */
    public function fileAction(Order $order, $type)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        $method = 'get' . ucfirst($type);

        if (!method_exists($order, $method)) {
            $message = 'The class %s does not have %s file';
            throw $this->createNotFoundException(sprintf($message, get_class($order), $type));
        }

        $filename = $order->$method();

        if (!$filename) {
            $message = 'File %s not found';
            throw $this->createNotFoundException(sprintf($message, $type));
        }

        $options = [
            'filename' => $filename,
            'root' => 'order',
            'type' => ($type == 'proforma') ? 'proforma' : 'payment',
            'access' => 'private'
        ];

        $file = $this->container->get('app_storage')->display($options);

        if (is_file($file)) {
            $header = ResponseHeaderBag::DISPOSITION_INLINE;
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
}
