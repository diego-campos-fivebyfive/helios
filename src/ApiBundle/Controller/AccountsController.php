<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\MemberInterface;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Customer;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AccountsController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="This method return a specific account"
     * )
     */
    public function getAccountAction(Customer $id)
    {
        $data = [];
        $account = $id;
        if($account->isAccount()) {

            $data = [
                'id' => $account->getId(),
                'firstname' => $account->getFirstname(),
                'lastname' => $account->getLastname(),
                'email' => $account->getEmail(),
                'phone' => $account->getPhone(),
                'owner' => $account->getOwner()->getId()
            ];

            $members = $account->getMembers()->map(function (MemberInterface $member) {
                return $member->getId();
            });

            $data['users'] = $members;
        }

        $view = View::create($data);

        return $this->handleView($view);
    }
}
