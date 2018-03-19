<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Service\Postcode\Finder;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("utils")
 */
class UtilsController extends AbstractController
{
    /**
     * @Route("/postcode", name="utils_postcode")
     * @Method("post")
     */
    public function postcodeAction(Request $request)
    {
        $postcode = $request->request->get('postcode');

        /** @var Finder $postcodeFinder */
        $postcodeFinder = $this->get('postcode_finder');

        return $this->json($postcodeFinder->find($postcode));
    }
}
