<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\User;
use AppBundle\Model\Document\Account;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Customer;
use FOS\RestBundle\View\View;
use AppBundle\Entity\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends FOSRestController
{

    public function postUserAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /** @var AccountInterface $accountManager */
        $accountManager = $this->get('account_manager');
        $account = $accountManager->find($data['account_id']);

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEmail($data['email'])
             ->setUsername($data['email'])
             ->setPlainPassword(uniqid())
             ->addRole(UserInterface::ROLE_OWNER_MASTER);
        $userManager->updateUser($user);

        /** @var AccountInterface $memberManager */
        $memberManager = $this->get('account_manager');
        $member = $memberManager->create();
        $member ->setAccount($account)
                ->setFirstname($data['contact'])
                ->setPhone($data['phone'])
                ->setEmail($data['email'])
                ->setContext(Customer::CONTEXT_MEMBER)
                ->setUser($user);
        $memberManager->save($member);

        return JsonResponse::create($member, 201);
    }
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="This method return a specific user"
     * )
     */
    public function getUserAction(Customer $id)
    {
        $data = [];
        $member = $id;

        if($member->isMember()) {

            $data = [
                'id' => $member->getId(),
                'firstname' => $member->getFirstname(),
                'lastname' => $member->getLastname(),
                'email' => $member->getEmail(),
                'phone' => $member->getPhone(),
                'account' => $member->getAccount()->getId()
            ];
        }

        $view = View::create($data);

        return $this->handleView($view);
    }

    public function putUserAction(Request $request, Customer $id)
    {
        $data = json_decode($request->getContent(), true);

        $member = $id;

        if (!$member->isMember()) {
            return JsonResponse::create("Invalid Member ID", Response::HTTP_NOT_FOUND);
        }

        /** @var AccountInterface $accountManager */
        $accountManager = $this->get('account_manager');
        $account = $accountManager->find($data['account_id']);

        /** @var AccountInterface $memberManager */
        $memberManager = $this->get('account_manager');
        $member ->setAccount($account)
            ->setFirstname($data['contact'])
            ->setPhone($data['phone'])
            ->setEmail($data['email']);

        try {
            $memberManager->save($member);
            $status = Response::HTTP_ACCEPTED;
        }catch (\Exception $exception){
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not update Member';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);

    }
 }
