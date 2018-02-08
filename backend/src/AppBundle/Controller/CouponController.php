<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Misc\Coupon;
use AppBundle\Entity\Misc\CouponInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/coupon/api")
 *
 * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
 */
class CouponController extends AbstractController
{
    /**
     * @Route("/", name="list_coupon")
     *
     * @Method("get")
     */
    public function indexAction(Request $request)
    {
        $qb = $this->manager('coupon')->createQueryBuilder();

        if (!empty($accounts = $request->get('account'))) {
            $arrayAccounts = array_filter(explode(',', $accounts));
            $qb->andWhere($qb->expr()->in('c.account', $arrayAccounts));
        }

        if (!empty($name = $request->get('name'))) {
            $arrayNames = array_filter(explode(',', $name));
            $qb->andWhere($qb->expr()->in('c.name', $arrayNames));
        }

        if (null != $status = $request->get('status')) {
            $status ? $qb->andWhere('c.target is not null') : $qb->andWhere('c.target is null');
        }

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), $itemsPerPage
        );

        $data = $this->formatCollection($pagination);

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="create_coupon")
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

    /**
     * @Route("/{id}", name="update_coupon")
     *
     * @Method("put")
     */
    public function updateAction(Request $request, Coupon $coupon)
    {
        $name = $request->request->get('name');
        $amount = $request->request->get('amount');
        $accountId = $request->request->get('account');

        $accountManager = $this->manager('account');
        $account = $accountManager->findOneBy([
            'id' => $accountId,
            'context' => BusinessInterface::CONTEXT_ACCOUNT
        ]);

        $coupon->setName($name);
        $coupon->setAmount($amount);
        if ($account)
            $coupon->setAccount($account);

        $this->manager('coupon')->save($coupon);

        return $this->json([]);
    }

    /**
     * @Route("/{id}", name="delete_coupon")
     *
     * @Method("delete")
     */
    public function deleteAction(Coupon $coupon)
    {
        $manager = $this->manager('coupon');

        $manager->delete($coupon);

        return $this->json([]);
    }

    /**
     * @param $couponCollection
     * @return array
     */
    private function formatEntity($couponCollection)
    {
        $data = [];
        foreach ($couponCollection as $coupon) {
            $account = $coupon->getAccount() ? $coupon->getAccount()->getFirstName() : null;

            $data [] = [
                'id' => $coupon->getId(),
                'name' => $coupon->getName(),
                'amount' => $coupon->getAmount(),
                'target' => $coupon->getTarget(),
                'account' => $account,
                'applied' => $coupon->isApplied()
            ];
        }
        return $data;
    }

    /**
     * @param $pagination
     * @param $position
     * @return bool|string
     */
    private function getPaginationLinks($pagination, $position)
    {
        if ($position == 'previous') {
            return $pagination['current'] > 1 ? "/coupon/?page={$pagination[$position]}" : false;
        }

        if ($position == 'next') {
            return $pagination['current'] < $pagination['last'] ? "/coupon/?page={$pagination[$position]}" : false;
        }

        return "/coupon/?page={$pagination[$position]}";
    }

    /**
     * @param $collection
     * @return array
     */
    private function formatCollection($collection)
    {
        $pagination = $collection->getPaginationData();

        return [
            'page' => [
                'total' => $pagination['pageCount'],
                'current'=> $pagination['current'],
                'links' => [
                    'prev' => $this->getPaginationLinks($pagination, 'previous'),
                    'self' => $this->getPaginationLinks($pagination, 'current'),
                    'next' => $this->getPaginationLinks($pagination, 'next')
                ]
            ],
            'size' => $pagination['totalCount'],
            'limit' => $pagination['numItemsPerPage'],
            'results' => $this->formatEntity($collection->getItems())
        ];
    }
}
