<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Manager\AccountManager;
use AppBundle\Model\Document\Account;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/v1/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/available", name="available_accounts")
     *
     * @Method("get")
     */
    public function accountsAction(Request $request)
    {
        $qb = $this->manager('account')->createQueryBuilder();

        $qb->select('a.id, a.firstname as name')
            ->from(Customer::class, 'a')
            ->where('a.status = :status')
            ->andWhere('a.context = :context')
            ->andWhere('a.email <> :email_sices')
            ->setParameters([
                'status' => AccountInterface::ACTIVATED,
                'context' => BusinessInterface::CONTEXT_ACCOUNT,
                'email_sices' => 'servidor@sicesbrasil.com.br'
            ])
            ->groupBy('a.id')
            ->setMaxResults(10);

        $search = $request->get('search');

        if ($search) {
            $qb->andWhere(
                $qb->expr()->like(
                    'a.firstname',
                    $qb->expr()->literal('%'.$search.'%')
                    ));
        }

        return $this->json($qb->getQuery()->getResult(), Response::HTTP_OK);
    }

    /**
     * @Route("/", name="all_accounts")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("get")
     */
    public function getListAction(Request $request)
    {
        $qb = $this->manager('account')->createQueryBuilder();

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), $itemsPerPage
        );

        $data = $this->formatCollection($pagination);

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="single_account")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("get")
     */
    public function getSingleAction(Customer $account)
    {
        $users = [];

        /** @var Customer $user */
        foreach ($account->getMembers() as $user) {
            $userData['name'] = $user->getName();
            $userData['email'] = $user->getEmail();
            $userData['userLevel'] = $user->getType();

            $users[] = $userData;
        }

        $data = [
            'name' => $account->getName(),
            'phone' => $account->getPhone(),
            'address' => $account->getAddress(),
            'firstname' => $account->getFirstname(),
            'lastname' => $account->getLastname(),
            'postcode' => $account->getPostcode(),
            'state' => $account->getState(),
            'level' => $account->getLevel(),
            'extraDocument' => $account->getExtraDocument(),
            'city' => $account->getCity(),
            'activatedAt' => $account->getActivatedAt(),
            'email' => $account->getEmail(),
            'district' => $account->getDistrict(),
            'agent' => $account->getAgent(),
            'users' => $users,
            'owner' => $account->getOwner()->getId()
        ];

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="create_account")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /** @var AccountManager $manager */
        $manager = $this->manager('account');
        $emailAlreadyInUse = $manager->findOneBy([
            'context' => 'account',
            'email' => $data['email']
        ]);

        if ($emailAlreadyInUse) {
            $data = "This email already exists!";
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;

            return $this->json($data, $status);
        }

        $documentAlreadyInUse = $manager->findOneBy([
            'context' => 'account',
            'document' => $data['document']
        ]);

        if ($documentAlreadyInUse) {
            $data = "This CNPJ already exists!";
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;

            return $this->json($data, $status);
        }

        /** @var AccountInterface $account */
        $account = $manager->create();
        $account
            ->setDocument($data['document'])
            ->setExtraDocument($data['extraDocument'])
            ->setFirstName($data['firstname'])
            ->setLastName($data['lastname'])
            ->setPostcode($data['postcode'])
            ->setState($data['state'])
            ->setCity($data['city'])
            ->setDistrict($data['district'])
            ->setStreet($data['street'])
            ->setNumber($data['number'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'])
            ->setStatus($data['status'])
            ->setContext(Customer::CONTEXT_ACCOUNT)
            ->setLevel($data['level'])
            ->setAgent($data['agent'])
            ->setParentAccount($data['parentAccount']);

        try {
            $manager->save($account);
            $status = Response::HTTP_CREATED;
            $data = [
                'id' => $account->getId(),
                'firstname' => $account->getFirstName(),
                'lastname' => $account->getLastName(),
                'extraDocument' => $account->getExtraDocument(),
                'document' => $account->getDocument(),
                'email' => $account->getEmail(),
                'state' => $account->getState(),
                'city' => $account->getCity(),
                'phone' => $account->getPhone(),
                'district' => $account->getDistrict(),
                'street' => $account->getStreet(),
                'number' => $account->getNumber(),
                'postcode' => $account->getPostcode(),
                'level' => $account->getLevel(),
                'status' => $account->getStatus(),
                'agent' => $account->getAgent()->getId(),
                'parentAccount' => $account->getParentAccount()->getId()
            ];
        } catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = $exception;
        }

        return $this->json($data, $status);
    }

    /**
     * @Route("/", name="create_account")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("put")
     */
    public function updateAction(Request $request, Customer $account)
    {
        $data = json_decode($request->getContent(), true);

        if (!$account->isAccount()) {
            $data = "Invalid Account ID";
            $status = Response::HTTP_NOT_FOUND;

            return $this->json($data, $status);
        }

        /** @var AccountInterface $accountManager */
        $accountManager = $this->manager('account');
        $account
            ->setFirstName($data['firstname'])
            ->setLastName($data['lastname'])
            ->setExtraDocument($data['extraDocument'])
            ->setDocument($data['document'])
            ->setEmail($data['email'])
            ->setState($data['state'])
            ->setCity($data['city'])
            ->setPhone($data['phone'])
            ->setDistrict($data['district'])
            ->setStreet($data['street'])
            ->setNumber($data['number'])
            ->setPostcode($data['postcode'])
            ->setStatus($data['status'])
            ->setLevel($data['level'])
            ->setAgent($data['agent'])
            ->setParentAccount($data['parentAccount']);

        try {
            $accountManager->save($account);
            $status = Response::HTTP_ACCEPTED;
            $data = [
                'id' => $account->getId(),
                'isquik_id' =>$account->getIsquikId(),
                'firstname' => $account->getFirstName(),
                'lastname' => $account->getLastName(),
                'extraDocument' => $account->getExtraDocument(),
                'document' => $account->getDocument(),
                'email' => $account->getEmail(),
                'state' => $account->getState(),
                'city' => $account->getCity(),
                'phone' => $account->getPhone(),
                'district' => $account->getDistrict(),
                'street' => $account->getStreet(),
                'number' => $account->getNumber(),
                'postcode' => $account->getPostcode(),
                'level' => $account->getLevel(),
                'status' => $account->getStatus(),
                'agent' => $account->getAgent()->getId(),
                'parentAccount' => $account->getParentAccount()->getId(),
                'owner' => $account->getOwner()->getId()
            ];
        } catch (\Exception $exception ) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = $exception;
        }

        return $this->json($data, $status);
    }

    /**
     * @param $accountCollection
     * @return array
     */
    private function formatEntity($accountCollection)
    {
        return array_map(function(Customer $account) {
            return [
                'id' => $account->getId(),
                'name' => $account->getName(),
                'document' => $account->getDocument(),
                'email' => $account->getEmail(),
                'level' => $account->getLevel(),
                'status' => $account->getStatus()
            ];
        }, $accountCollection);
    }

    /**
     * @param $pagination
     * @param $position
     * @return bool|string
     */
    private function getPaginationLinks($pagination, $position)
    {
        if ($position == 'previous') {
            return $pagination['current'] > 1 ? "/account/?page={$pagination[$position]}" : false;
        }

        if ($position == 'next') {
            return $pagination['current'] < $pagination['last'] ? "/account/?page={$pagination[$position]}" : false;
        }

        return "/account/?page={$pagination[$position]}";
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
