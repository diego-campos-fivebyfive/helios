<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("fiscal")
 */
class FiscalController extends AbstractController
{
    /**
     * @Route("/danfe")
     */
    public function danfeAction(Request $request)
    {
        if (!$this->getAuth($request)) {
            return $this->json([]);
        }

        $this->get('nfe_core')->core();

        return $this->json([]);
    }

    /**
     * @Route("/proceda")
     */
    public function procedaAction(Request $request)
    {
        if (!$this->getAuth($request)) {
            return $this->json([]);
        }

        $this->get('proceda_processor')->resolve();

        return $this->json([]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function getAuth(Request $request)
    {
        $auth = "OewkQ42mCxVyfk7cbKg5jORFTWdWMQhxIO2bjHQt";
        $secret = "NXTh0oqmwed4PvK3HCysMJjMWEGGJ2Fw0hXDfyox";
        $header = $request->server->getHeaders();

        return $header['AUTHORIZATION'] === $auth && $header['SECRET'] === $secret;
    }
}
