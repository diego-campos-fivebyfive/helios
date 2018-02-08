<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/info", name="user_info")
     *
     * @Method("get")
     */
    public function userInfo()
    {
        $member = $this->member();
        $account = $member->getAccount();
        return $this->json([
            'user' => [
                'name' => $member->getFirstname()
            ],
            'account' => [
                'name' => $account->getFirstname(),
                'level' => $account->getLevel()
            ]
        ]);
    }
}
