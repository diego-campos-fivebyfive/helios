<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order\Order;
use AppBundle\Service\Mailer;
use AppBundle\Service\Order\OrderExporter;
use AppBundle\Service\Order\OrderFinder;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("debug")
 * @Security("has_role('ROLE_PLATFORM_MASTER')")
 */
class DebugController extends AbstractController
{
    /**
     * @Route("/orders")
     */
    public function orderCouponAction()
    {
        /** @var OrderFinder $finder */
        $finder = $this->get('order_finder');

        /** @var OrderExporter $exporter */
        $exporter = $this->get('order_exporter');

        $qb = $finder->queryBuilder();

        $qb
            ->andWhere('o.status >= ' . Order::STATUS_APPROVED)
            ->andWhere('o.fileExtract IS NULL');

        $orders = $qb->getQuery()->getResult();

        $ids = [];
        /** @var Order $order */
        foreach ($orders as $order) {
            if (!$order->getChildrens()->isEmpty() && !$order->getFileExtract()) {

                $ids[] = $order->getId();

                // Manter comentado execução do deploy
                // $exporter->exportLegacy($order);

                if(count($ids) >= 10){
                    break;
                }
            }
        }

        // Manter estes procedimentos para controle
        var_dump($ids);
        die(sprintf('<br> %d orçamentos corrigidos.', count($ids)));
    }

    /**
     * @Route("/{id}/email")
     */
    public function emailAction(Request $request, Order $order)
    {
        //dump($order->getMessages()->last()->isRestricted());die;

        /** @var Mailer $mailerService */
        $mailerService = $this->container->get('app_mailer');

        $mailerService->sendOrderMessage($order);

    }
}
