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
     * @Route("/api", name="user_info")
     *
     * @Method("get")
     */
    public function infoAction()
    {
        $member = $this->member();
        $account = $member->getAccount();

        return $this->json([
            'name' => $member->getFirstname(),
            'company' => $account->getFirstname(),
            'level' => $account->getLevel()
        ]);
    }
}
