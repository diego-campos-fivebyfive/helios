<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Misc\Coupon;
use AppBundle\Entity\Misc\CouponInterface;
use AppBundle\Service\Coupon\Transformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("api/v1/coupon")
 *
 */
class CouponController extends AbstractController
{
    /**
     * @Route("/", name="list_coupon")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("get")
     */
    public function getAction(Request $request)
    {
        $qb = $this->manager('coupon')->createQueryBuilder();
        $qb->orderBy('c.id', 'DESC');

        $accounts = $request->get('account');
        $name = $request->get('name');
        $status = $request->get('status');

        if (!empty($accounts)) {
            $accounts = array_filter(explode(',', $accounts));
            $qb->andWhere($qb->expr()->in('c.account', $accounts));
        }

        if (!empty($name)) {
            $names = array_filter(explode(',', $name));
            $qb->andWhere($qb->expr()->in('c.name', $names));
        }

        if ($status) {
            $status
                ? $qb->andWhere('c.target is not null')
                : $qb->andWhere('c.target is null');
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
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $name = $request->request->get('name');
        $amount = $request->request->get('amount');
        $accountId = $request->request->get('account');

        $validator = Validation::createValidator();

        $constraint = new Assert\Collection([
            'name' => new Assert\NotBlank([
                'message' => 'O campo nome deve estar preenchido'
            ]),
            'amount' => new Assert\NotBlank([
                'message' => 'O campo valor deve estar preenchido'
            ])
        ]);

        $violations = $validator->validate(['name' => $name, 'amount'=> $amount], $constraint);

        $errors = [];
        foreach ($violations as $violation) {
            $errors[str_replace(['[',']'],'', $violation->getPropertyPath())] = $violation->getMessage();
        }

        if ($violations->count()) {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $account = $this->manager('account')->findOneBy([
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
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("put")
     */
    public function updateAction(Request $request, Coupon $coupon)
    {
        $this->denyAccessUnlessGranted('edit', $coupon);

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
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("delete")
     */
    public function deleteAction(Coupon $coupon)
    {
        $this->denyAccessUnlessGranted('edit', $coupon);

        $manager = $this->manager('coupon');

        $manager->delete($coupon);

        return $this->json([]);
    }

    /**
     * @Route("/code/{code}", name="get_coupon")
     *
     * @Method("get")
     */
    public function getCouponAction($code)
    {
        /** @var Transformer $couponTransformer */
        $couponTransformer = $this->container->get("coupon_transformer");

        $coupon = $couponTransformer->getCoupon($code);

        if (!$coupon) {
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('edit', $coupon);

        $couponArray = [
            "id" => $coupon->getId(),
            "code" => $coupon->getCode(),
            "name" => $coupon->getName(),
            "amount" => $coupon->getAmount(),
            "applied" => $coupon->isApplied(),
            "appliedAt" => $coupon->getAppliedAt()->format("Y-m-d"),
            "target" => $coupon->getTarget()
        ];

        return $this->json($couponArray);
    }

    /**
     * @param $couponCollection
     * @return array
     */
    private function formatEntity($couponCollection)
    {
        return array_map(function($coupon) {
            $account = $coupon->getAccount();

            if ($account) {
                $account = [
                    'id' => $account->getId(),
                    'name' => $account->getFirstName()
                ];
            } else {
                $account = [
                    'id' => '',
                    'name' => ''
                ];
            }

            return [
                'id' => $coupon->getId(),
                'name' => $coupon->getName(),
                'amount' => $coupon->getAmount(),
                'target' => $coupon->getTarget(),
                'account' => $account,
                'applied' => $coupon->isApplied()
            ];
        }, $couponCollection);
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
