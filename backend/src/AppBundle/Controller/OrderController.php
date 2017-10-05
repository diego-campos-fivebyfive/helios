<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order\Order;
use AppBundle\Service\Pricing\Insurance;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
        $manager = $this->manager('order');

        $qb = $manager->createQueryBuilder();
        $qb2 = $manager->getEntityManager()->createQueryBuilder();
        $qb->where(
            $qb->expr()->in('o.id',
                $qb2->select('o2')
                    ->from(Order::class, 'o2')
                    ->where('o2.parent is null')
                ->getQuery()->getDQL()
            )
        )->andWhere('o.account = :account')
        ->setParameter('account', $this->account());

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('order.index', array(
            'orders' => $pagination
        ));
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

            if ($order->isDone()){
                // TODO Generate Proforma
            }

            // TODO (Email): Manter comentado até aprovação de layouts
            //$this->get('order_mailer')->sendOrderMessage($order);

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

        // TODO (Email): Manter comentado até aprovação de layouts
        //$this->get('order_mailer')->sendOrderMessage($order);

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

                $dir = $this->getUploadDir();

                $current = $dir . $order->getFilePayment();

                if(is_file($current)) unlink($current);

                $name = sprintf(
                    'payment_file_%s_%s.%s', $order->getId(),
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
     * @Route("/{id}/file", name="order_file")
     */
    public function fileAction(Order $order)
    {
        if(null != $filePayment = $order->getFilePayment()) {

            $file = $this->getUploadDir() . $filePayment;

            return new BinaryFileResponse($file, Response::HTTP_OK, [], true, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        }

        throw $this->createNotFoundException('File not found');
    }

    /**
     * @Route("/{id}/generator", name="proforma_pdf_generator")
     */
    public function generatorProformaAction(Order $order)
    {
        $status = Response::HTTP_CONFLICT;
        $filename = null;

        if ($order) {

            $snappy = $this->get('knp_snappy.pdf');
            $snappy->setOption('viewport-size', '1280x1024');
            $snappy->setOption('margin-top', 0);
            $snappy->setOption('margin-bottom', 0);
            $snappy->setOption('margin-left', 0);
            $snappy->setOption('margin-right', 0);
            $snappy->setOption('zoom', 2);

            $dir = $this->get('kernel')->getRootDir() . '/../storage/';
            $filename = 'proforma_' . $order->getId() . '.pdf';
            $file = $dir . $filename;

            $url = $this->generateUrl('proforma_pdf', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            try {

                $snappy->generate($url, $file);

                if (file_exists($file)) {
                    $status = Response::HTTP_OK;
                }

            } catch (\Exception $error) {
            }
        }

        return $this->json([
            'filename' => $filename
        ], $status);
    }


    /**
     * @return string
     */
    private function getUploadDir()
    {
        return $this->get('kernel')->getRootDir() . '/../storage/orders/';
    }
}
