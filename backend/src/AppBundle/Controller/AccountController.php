<?php

namespace AppBundle\Controller;

use AppBundle\Configuration\Brazil;
use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Manager\AccountManager;
use AppBundle\Manager\CustomerManager;
use AppBundle\Entity\Pricing\Memorial;
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

        $qb->select('c.id, c.firstname as name')
            ->where('c.status = :status')
            ->andWhere('c.context = :context')
            ->andWhere('c.email <> :email_sices')
            ->setParameters([
                'status' => AccountInterface::ACTIVATED,
                'context' => BusinessInterface::CONTEXT_ACCOUNT,
                'email_sices' => 'servidor@sicesbrasil.com.br'
            ])
            ->setMaxResults(10);

        $search = $request->get('search');

        if ($search) {
            $qb->andWhere(
                $qb->expr()->like(
                    'c.firstname',
                    $qb->expr()->literal('%'.$search.'%')
                    ));
        }

        return $this->json($qb->getQuery()->getResult(), Response::HTTP_OK);
    }

    /**
     * @Route("/states", name="all_states")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("get")
     */
    public function getStates()
    {
        $data = Brazil::states();

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/levels", name="account_levels_api")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("get")
     */
    public function getLevels()
    {
        $data = Memorial::getDefaultLevels();

        unset($data['promotional'], $data['finame']);

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/agents/{id}", name="account_agents")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("get")
     */
    public function getAgents(Customer $account)
    {
        $members = $account->getMembers()->filter(function ($member) {
            return $member->isPlatformCommercial();
        });

        $data = [];

        foreach ($members as $member) {
            $data[] = $member->toArray();
        }

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/parent_accounts/{id}", name="account_levels")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("get")
     */
    public function getPossibleParents(Customer $account)
    {

        $qb = $this->manager('account')->createQueryBuilder();

        $qb
            ->where('c.context = :context')
            ->andWhere('c.parent is null')
            ->andWhere('c.status = :status')
            ->setParameters([
                'context' => BusinessInterface::CONTEXT_ACCOUNT,
                'status' => 3
            ])
        ;

        if ($account->getId()) {
            $qb
                ->andWhere('c.id <> :thisAccount')
                ->setParameter(
                    'thisAccount', $account
                )
            ;
        }

        $data = [];

        $possibleParentAccounts = $qb->getQuery()->getResult();

        foreach ($possibleParentAccounts as $account) {
            $accountData['id'] = $account->getId();
            $accountData['name'] = $account->getName();

            $data[] = $accountData;
        }

        return $this->json($data, Response::HTTP_OK);
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
            'street' => $account->getAddress(),
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
            'agent' => $account->getAgent() ? $account->getAgent()->getName() : null,
            'users' => $users,
            'owner' => $account->getOwner()->getFirstname(),
            'persistent' => $account->isPersistent(),
            'number' => $account->getNumber()
        ];

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/", name="create_account_api")
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN') or has_role('ROLE_AFTER_SALES') or has_role('ROLE_PLATFORM_COMMERCIAL') or has_role('ROLE_PLATFORM_EXPANSE') or has_role('ROLE_PLATFORM_FINANCIAL') or has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /** @var CustomerManager $manager */
        $manager = $this->manager('customer');

        $agent = !empty($data['agent']) ? $data['agent'] : null;

        $parentAccount = !empty($data['parentAccount']) ? $data['parentAccount'] : null;

        $errors = $this->validateData($data, $manager, $agent, $parentAccount);

        if ($errors) {
            return $errors;
        }

        /** @var AccountInterface $account */
        $account = $manager->create();
        $account->setContext(Customer::CONTEXT_ACCOUNT);

        $this->setValues($account, $data, $agent, $parentAccount);

        /** @var MemberInterface $member */
        $member = $manager->create();
        $member->setContext(Customer::CONTEXT_MEMBER);

        $account->addMember($member);

        $member->setEmail($account->getEmail());

        $this->createUser($member);

        try {
            $manager->save($account);
        } catch (\Exception $exception) {
            return $this->json([
                'error' => $exception
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($account->isApproved()) {
            $this->getMailer()->sendAccountConfirmationMessage($account);
        }

        return $this->json([], Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="update_account_api")
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
     * @Route("/{id}/switch-owner", name="switch_account_owner_api")
     *
     * @Security("has_role('ROLE_PLATFORM_AFTER_SALES') or has_role('ROLE_PLATFORM_EXPANSE')")
     *
     * @Method("post")
     */
    public function switchOwnerAction(Request $request, Customer $account)
    {
        $status = Response::HTTP_OK;

        if ($account->isAccount()) {
            $targetId = $request->request->get('target');

            /** @var CustomerManager $customerManager */
            $customerManager = $this->manager('customer');

            /** @var MemberInterface $newOwer */
            $newOwer = $customerManager->find($targetId);
            $owner = $account->getOwner();

            if ($this->belongsToAccount($newOwer, $account) && $newOwer !== $owner) {
                $owner->getUser()->removeRole(UserInterface::ROLE_OWNER_MASTER);
                $newOwer->getUser()->addRole(UserInterface::ROLE_OWNER_MASTER);

                $customerManager->save($account);
            } else {
                $status = Response::HTTP_BAD_REQUEST;
            }
        } else {
            $status = Response::HTTP_BAD_REQUEST;
        }

        return $this->json([], $status);
    }

    private function belongsToAccount($member, $account)
    {
        $accountMembers = $account->getMembers();

        foreach ($accountMembers as $accountMember) {
            if ($member === $accountMember) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $data
     * @param $manager
     * @param $agent
     * @param $parentAccount
     * @return null|\Symfony\Component\HttpFoundation\JsonResponse
     */
    private function validateData($data, CustomerManager $manager, &$agent, &$parentAccount)
    {
        $helper = $this->get('app.register_helper');

        $error = null;

        if (!$helper->emailCanBeUsed($data['email'])) {
            $error = "This email is already in use!";
        }

        $documentAlreadyInUse = $manager->findOneBy([
            'document' => $data['document']
        ]);

        if ($documentAlreadyInUse) {
            $error = "This CNPJ already exists!";
        }

        if ($agent) {
            $agent = $manager->findOneBy([
                'context' => 'member',
                'id' => $agent
            ]);

            if (!$agent) {
                $error = "Agent not found!";
            }
        }

        if ($parentAccount) {
            $parentAccount = $manager->findOneBy([
                'context' => 'account',
                'id' => $parentAccount
            ]);

            if (!$parentAccount) {
                $error = "Parent account not found!";
            }
        }

        return $error ? $this->json(['error' => $error], $status = Response::HTTP_UNPROCESSABLE_ENTITY) : null;
    }

    /**
     * @return \FOS\UserBundle\Util\TokenGenerator
     */
    private function getTokenGenerator()
    {
        return $this->get('fos_user.util.token_generator');
    }

    /**
     * @return \AppBundle\Service\Mailer
     */
    private function getMailer()
    {
        return $this->get('app_mailer');
    }

    /**
     * @param AccountInterface $account
     * @param $data
     * @param $agent
     * @param $parentAccount
     */
    private function setValues(AccountInterface $account, $data, $agent, $parentAccount)
    {
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
            ->setStatus(Customer::APPROVED)
            ->setContext(Customer::CONTEXT_ACCOUNT);


        if (!$this->member()->isPlatformAdmin() && !$this->member()->isPlatformMaster()) {
            $account->setLevel(Memorial::LEVEL_PARTNER);
        } else {
            $account->setLevel($data['level']);
        }

        $agent = $this->member()->isPlatformCommercial() ? $this->member() : $agent;

        $account->setAgent($agent);

        $account->setParentAccount($parentAccount);

        if ($parent = $account->getParentAccount()) {
            $account->setAgent($parent->getAgent());
            $account->setLevel($parent->getLevel());
        }

        $account->setConfirmationToken($this->getTokenGenerator()->generateToken());
    }

    /**
     * @param MemberInterface $member
     */
    private function createUser(MemberInterface $member)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->createUser();

        $user
            ->setEmail($member->getEmail())
            ->setUsername($member->getEmail())
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_OWNER,
                UserInterface::ROLE_OWNER_MASTER
            ]);

        $member->setUser($user);
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
