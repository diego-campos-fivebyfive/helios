<?php

namespace AppBundle\Controller;

use AppBundle\Service\Business\DataCollector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
            'level' => $account->getLevel(),
            'token' => $member->getToken(),
            'sices' => $member->isPlatformUser()
        ]);
    }

    /**
     * @Route("/track-account", name="user_track_account")
     *
     * @Method("get")
     */
    public function trackAccount(Request $request)
    {
        $context = $request->get('context');

        $collector = DataCollector::create($this->container)->data();

        $data = ($context === 'intercom') ?
            array_merge(['app_id' => 't2yycetv'], $collector) : $collector;

        return $this->json($data);
    }
}
