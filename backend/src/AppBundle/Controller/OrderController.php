<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order\Order;
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
        /** @var \AppBundle\Service\Order\OrderFinder $finder */
        $finder = $this->get('order_finder');

        $finder->set('member', $this->member());

        $pagination = $this->getPaginator()->paginate(
            $finder->query(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('order.index', array(
            'orders' => $pagination
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
     * @Route("/{id}/insure", name="order_insure")
     * @Method("post")
     */
    public function insureAction(Order $order, Request $request)
    {
        Insurance::apply($order, (bool) $request->get('insure'));

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
        if($request->isMethod('post')){

            $file = $request->files->get('file');

            if($file instanceof UploadedFile) {

                $dir = $this->getUploadDir('filePayment');

                $current = $dir . $order->getFilePayment();

                if(is_file($current)) unlink($current);

                $name = sprintf(
                    'filePayment_%s_%s.%s', $order->getId(),
                    (new \DateTime())->format('Ymd-His'),
                    $file->getClientOriginalExtension()
                );

                $file->move($dir, $name);

                $order->setFilePayment($name);

                $this->manager('order')->save($order);

                return $this->json([
                    'name' => $name
                ]);
            }
        }

        return $this->render('order.upload', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/{id}/file/{type}", name="order_file")
     */
    public function fileAction(Order $order, $type)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        $getter = 'get' . ucfirst($type);

        if(!method_exists($order, $getter))
            throw $this->createNotFoundException(sprintf('The class %s does not have %s file', get_class($order), $type));

        if(null != $filename = $order->$getter()){

            $file = $this->getUploadDir($type) . $filename;

            if(is_file($file)) {
                return new BinaryFileResponse($file, Response::HTTP_OK, [], true, ResponseHeaderBag::DISPOSITION_INLINE);
            }
        }

        throw $this->createNotFoundException(sprintf('File %s not found', $type));
    }

    /**
     * @Route("/{id}/generator", name="proforma_pdf_generator")
     */
    public function generatorProformaAction(Order $order)
    {
        $status = Response::HTTP_CONFLICT;
        $filename = null;

        if ('prod' == $this->get('kernel')->getEnvironment()) {

            $snappy = $this->get('knp_snappy.pdf');
            $snappy->setOption('viewport-size', '1280x1024');
            $snappy->setOption('margin-top', 0);
            $snappy->setOption('margin-bottom', 0);
            $snappy->setOption('margin-left', 0);
            $snappy->setOption('margin-right', 0);
            $snappy->setOption('zoom', 2);

            $filename = sprintf('proforma_%s_%s_.pdf', $order->getId(), (new \DateTime())->format('Ymd-His'));
            $file = $this->getUploadDir('proforma') . $filename;

            $url = $this->generateUrl('proforma_pdf', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            try {

                $snappy->generate($url, $file);

                if (file_exists($file)) {
                    $status = Response::HTTP_OK;
                }

                $order->setProforma($filename);

                $this->manager('order')->save($order);

            } catch (\Exception $exception) {

                return $this->json([
                    'error' => $exception->getMessage()
                ], $status);
            }
        }

        return $this->json([
            'filename' => $filename
        ], $status);
    }

    /**
     * @Route("/{id}/delete", name="order_delete")
     * @Method("delete")
     */
    public function deleteAction(Order $order)
    {
        $this->denyAccessUnlessGranted('edit', $order);

        if(!$order->isBuilding()){
            return $this->json([
                'error' => 'Somente orçamentos em edição podem ser excluídos'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->manager('order')->delete($order);

        return $this->json([]);
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
