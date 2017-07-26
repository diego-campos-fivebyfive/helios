<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Model\Document\Account;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Customer;
use FOS\RestBundle\View\View;
use AppBundle\Entity\UserInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $view = View::create([
            'firstname' => $member->getFirstname(),
            'lastname' => $member->getLastname(),
            'email' => $member->getEmail(),
            'phone' => $member->getPhone()
        ]);
        return JsonResponse::create($view, 201);
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
 }
