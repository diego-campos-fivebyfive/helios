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
 * @Route("/api/v1/")
 */
class analyticsController extends AbstractController
{
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
            array_merge(['app_id' => 't2yycetv'], $collector) 
            : $collector;

        return $this->json($data);
    }
}
