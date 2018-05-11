<?php
/**
 * Created by PhpStorm.
 * User: kolina-pc2
 * Date: 11/05/18
 * Time: 14:22
 */

namespace AppBundle\Controller;

use AppBundle\Service\Business\DataCollector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/v1")
 */
class analyticsController extends AbstractController
{
    /**
     * @Route("/track-account", name="user_track_account")
     *
     * @Method("get")
     */
    public function trackAccount()
    {
        $collector = DataCollector::create($this->container)->data();

        return $this->json($collector);
    }
}
