<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/api/v1/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_data")
     *
     * @Method("get")
     */
    public function infoAction()
    {
        $member = $this->member();
        $account = $member->getAccount();

        return $this->json([
            'id' => $member->getId(),
            'name' => $member->getFirstname(),
            'company' => $account->getFirstname(),
            'level' => $account->getLevel()
        ]);
    }
}
