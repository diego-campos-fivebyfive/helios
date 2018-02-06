<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Misc\CouponInterface;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/coupon")
 * @Breadcrumb("Cupom")
 *
 * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
 */
class CouponController extends AbstractController
{
    /**
     * @Route("/create", name="create_coupon")
     *
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $name = $request->request->get('name');
        $amount = $request->request->get('amount');
        $accountId = $request->request->get('account');

        $accountManager = $this->manager('account');
        $account = $accountManager->findOneBy([
            'id' => $accountId,
            'context' => BusinessInterface::CONTEXT_ACCOUNT
        ]);

        $couponManager = $this->manager('coupon');

        /** @var CouponInterface $coupon */
        $coupon = $couponManager->create();

        //TODO: campo not null, verificar valor
        $coupon->setCode(0);

        $coupon->setName($name);
        $coupon->setAmount($amount);
        if ($account)
            $coupon->setAccount($account);

        $couponManager->save($coupon);

        return $this->json([]);
    }
}
