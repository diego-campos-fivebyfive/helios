<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Cart;
use AppBundle\Entity\Kit\CartPool;
use AppBundle\Manager\CartManager;
use AppBundle\Service\Cart\CartPoolHelper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("purchase")
 *
 * @Breadcrumb("Compra")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/create_cart_pool", name="cart_pool_create")
     * @Security("has_role('ROLE_OWNER')")
     * //@Method("post")
     */
    public function createCartPoolAction(Request $request)
    {
        $code = $request->get('code');

        $account = $this->account();

        /** @var CartPoolHelper $cartPoolHelper */
        $cartPoolHelper = $this->container->get('cart_pool_helper');

        $cartPoolHelper->createCartPool($code, $account);

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
     * @Method("get")
     */
    public function listCartPoolAction(Request $request)
    {
        $manager = $this->manager('cart_pool');

        $qb = $manager->createQueryBuilder();

        $qb
            ->orderBy('c.id', 'desc');

        if (!$this->member()->isPlatformUser()) {
            $qb->andWhere($qb->expr()->eq('c.account', $this->account()->getId()));
        }

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 10
        );

        return $this->render('cart.cart_pool_list', array(
            'pagination' => $pagination
        ));
    }

     /**
     * @Route("/cart_pool/{id}", name="cart_pool_detail")
     * @Security("has_role('ROLE_OWNER') or has_role('ROLE_PLATFORM_ADMIN')")
     */
    public function showCartPoolAction(CartPool $cartPool)
    {
        $isPlatform = $this->user()->isPlatformAdmin() || $this->user()->isPlatformMaster();
        $isAccountOwner = $cartPool->getAccount() === $this->account();

        if ($isPlatform || $isAccountOwner) {
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
        } else {
            $this->denyAccessUnlessGranted('view', $cartPool);
        }

    }
}
