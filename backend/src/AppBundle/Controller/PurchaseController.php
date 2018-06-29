<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Manager\CartManager;
use AppBundle\Manager\CartPoolManager;
use AppBundle\Service\Cart\CartPoolHelper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("twig/purchase")
 *
 * @Breadcrumb("Compra")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/{id}/finish_cart_pool", name="cart_pool_finish")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("post")
     */
    public function finishCartPoolAction(CartPool $cartPool)
    {
        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->container->get('cart_pool_helper');

        $cartPoolHelper->clearCart($this->getCart());

        /** @var CartPoolManager $cartPoolManager */
        $cartPoolManager = $this->manager('cart_pool');

        $cartPool->setConfirmed(true);

        $cartPoolManager->save($cartPool);

        return $this->json();
    }

    /**
     * @Route("/payment_feedback", name="payment_feedback")
     * @Security("has_role('ROLE_OWNER')")
     */
    public function paymentFeedbackAction()
    {
        return $this->render('cart.feedback', []);
    }

    /**
     * @Route("/list_cart_pool", name="list_cart_pool")
     * @Security("has_role('ROLE_OWNER') or has_role('ROLE_PLATFORM_ADMIN')")
     * @Breadcrumb("Histórico de Transações")
     * @Method("get")
     */
    public function listCartPoolAction(Request $request)
    {
        $manager = $this->manager('cart_pool');

        $qb = $manager->createQueryBuilder();

        $qb->orderBy('c.id', 'desc');

        if (!$this->member()->isPlatformUser()) {
            $qb->andWhere($qb->expr()->eq('c.account', $this->account()->getId()));
        }

        if (-1 != $status = $request->get('status')) {
            $status = explode(',', $status);
            $arrayStatus = array_filter($status, 'strlen');
            if (!empty($arrayStatus)) {
                $qb->andWhere($qb->expr()->in('c.status', $arrayStatus));
            }
        }

        $this->overrideGetFilters();

        $getStatuses = function ($statusList, $arrayStatus) {
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
            $request->query->getInt('page', 1), 10
        );

        return $this->render('cart.cart_pool_list', array(
            'pagination' => $pagination,
            'statusList' => $getStatuses(CartPool::getStatusNames(), $arrayStatus)
        ));
    }

     /**
     * @Route("/cart_pool/{id}", name="cart_pool_detail")
     * @Breadcrumb("Detalhes do Pedido")
     */
    public function showCartPoolAction(CartPool $cartPool)
    {
        $this->denyAccessUnlessGranted('view', $cartPool);

        $cartPoolTotal = 0;
        $kits = [];

        foreach ($cartPool->getItems() as $item) {
            $kitTotal = $item['value'] * $item['quantity'];
            $cartPoolTotal += $kitTotal;

            $kits[] = [
                'item' => $item,
                'quantity' => $item['quantity'],
                'total' => $kitTotal
            ];
        }

        $shipping = json_decode($cartPool->getCheckout()['shipping'], true)[0];

        return $this->render('cart.cart_pool_detail', [
            'cartPool' => $cartPool,
            'kits' => $kits,
            'total' => $cartPoolTotal,
            'shipping' => $shipping
        ]);

    }

    /**
     * @return Cart
     */
    private function getCart()
    {
        /** @var CartManager $cartManager */
        $cartManager = $this->manager('cart');

        /** @var Cart $cart */
        $cart = $cartManager->findOneBy([
            'account' => $this->account()
        ]);

        return $cart;
    }
}
