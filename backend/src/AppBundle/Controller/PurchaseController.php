<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Cart;
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
 * @Security("has_role('ROLE_OWNER')")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/create_cart_pool", name="cart_pool_create")
     * @Method("post")
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
     */
    public function paymentFeedbackAction()
    {
        return $this->render('cart.feedback', []);
    }
}
