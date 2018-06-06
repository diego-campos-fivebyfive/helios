<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Order\Order;
use AppBundle\Manager\CartManager;
use AppBundle\Service\Cart\CartPoolHelper;
use AppBundle\Service\Mailer;
use AppBundle\Service\Order\OrderExporter;
use AppBundle\Service\Order\OrderFinder;
use AppBundle\Service\Order\OrderTransformer;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("debug")
 * @Security("has_role('ROLE_OWNER')")
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

    /**
     * @Route("/cart_pool")
     */
    public function testCartPoolAction()
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        $cartHasKitManager = $this->manager('cart_has_kit');

        $items = $cartHasKitManager->findBy([
            'cart' => $cart
        ]);

        $code = md5(uniqid());
        $method = 'credito';

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->container->get('cart_pool_helper');

        $cartPool = $cartPoolHelper->formatItems($items);

        dump(json_encode($cartPool));die;

    }

    /**
     * @Route("/transform_cart")
     */
    public function testTransformCartAction()
    {
//        /** @var CartManager $cartManager */
//        $cartManager = $this->manager('cart');
//
//        /** @var Cart $cart */
//        $cart = $cartManager->findOneBy([
//            'account' => $this->account()
//        ]);
//
//        $cartHasKitManager = $this->manager('cart_has_kit');
//
//        $cartHasKit = $cartHasKitManager->findBy([
//            'cart' => $cart
//        ]);
//
//        $code = md5(uniqid());
//        $method = 'credito';
//
//        $checkoutData = [
//            "firstName" => 'Gianluca',
//            "lastName" => 'Bine',
//            "documentType" => 'CPF',
//            "document" => '088.463.559-70',
//            "email" => 'gian_bine@hotmail.com',
//            "phone" => '(42) 3623-8320',
//            "street" => 'Rua Teste',
//            "number" => '123',
//            "complement" => '',
//            "neighborhood" => 'Teste',
//            "city" => 'Teste',
//            "state" => 'PR',
//            "postcode" => '85015-310',
//            "country" => "Brasil",
//            "shippingName" => 'Gianluca Bine',
//            "shippingStreet" => 'Rua Teste',
//            "shippingComplement" => '',
//            "shippingNumber" => 123,
//            "shippingNeighborhood" => 'Teste',
//            "shippingCity" => 'Teste',
//            "shippingState" => 'PR',
//            "shippingPostcode" => '85015-310',
//            "differentDelivery" => true
//        ];
//
//        /** @var CartPoolHelper $cartPoolHelper */
//        $cartPoolHelper = $this->container->get('cart_pool_helper');
//
//        $items = $cartPoolHelper->formatItems($cartHasKit, false);
//        $checkout = $cartPoolHelper->formatCheckout($checkoutData);
//
//        $cartPool = $cartPoolHelper->create($code, $method, $this->account(), $items, $checkout);
//
//        dump($cartPool);die;

        /** @var CartPool $cartPoolManager */
        $cartPoolManager = $this->manager('cart_pool');

        $cartPool = $cartPoolManager->findOneBy([
            'code' => 'd95d6541b24990f85eb41fd5e9cfb08c'
        ]);

        /** @var OrderTransformer $orderTransformer */
        $orderTransformer = $this->container->get('order_transformer');

        $orderTransformer->transformFromCartPool($cartPool);

        dump($cartPool);die;
    }
}
