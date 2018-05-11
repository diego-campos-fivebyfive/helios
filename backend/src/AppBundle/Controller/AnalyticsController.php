<?php

namespace AppBundle\Controller;

use AppBundle\Service\Business\DataCollector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1")
 */
class AnalyticsController extends AbstractController
{
    /**
     * @Route("/track_account", name="user_track_account")
     *
     * @Method("get")
     */
    public function trackAccount()
    {
        $collector = DataCollector::create($this->container)->data();

        return $this->json($collector);
    }
}
