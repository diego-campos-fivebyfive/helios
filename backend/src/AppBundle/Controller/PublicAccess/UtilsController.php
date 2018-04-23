<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Service\Postcode\Finder;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class UtilsController extends AbstractController
{
    /**
     * @Route("/utils/postcode", name="utils_postcode")
     * @Method("post")
     */
    public function appPostcodeAction(Request $request)
    {
        $postcode = $request->request->get('postcode');

        return $this->findPostcode($postcode);
    }

    /**
     * @Route("/api/v1/utils/postcode/{postcode}", name="utils_postcode_api")
     * @Method("get")
     */
    public function apiPostcodeAction($postcode)
    {
        return $this->findPostcode($postcode);
    }

    /**
     * @param $postcode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function findPostcode($postcode)
    {
        /** @var Finder $postcodeFinder */
        $postcodeFinder = $this->get('postcode_finder');

        return $this->json($postcodeFinder->find($postcode));
    }
}
