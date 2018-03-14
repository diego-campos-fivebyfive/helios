<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("fiscal")
 */
class FiscalController extends AbstractController
{
    /**
     * @Route("/danfe")
     * @Method("post")
     */
    public function danfeAction(Request $request)
    {
        if (!$this->getAuth($request)) {
            return $this->json([], Response::HTTP_FORBIDDEN);
        }

        try {

            return $this->json($this->get('nfe_core')->core());

        }catch (\Exception $e){

            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    /**
     * @Route("/proceda")
     * @Method("post")
     */
    public function procedaAction(Request $request)
    {
        if (!$this->getAuth($request)) {
            return $this->json([], Response::HTTP_FORBIDDEN);
        }

        try{

            return $this->json($this->get('proceda_processor')->resolve());

        }catch (\Exception $e){

            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
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
