<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Customer;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UsersController extends FOSRestController
{
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
                'phone' => $member->getPhone()
            ];
        }

        $view = View::create($data);

        return $this->handleView($view);
    }
}
