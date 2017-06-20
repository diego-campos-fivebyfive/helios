<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class RandomController
 * @package AppBundle\Controller
 *
 * @Route("random")
 */
class RandomController extends AbstractController
{
    /**
     * @Route("/account", name="random_account")
     */
    public function accountAction()
    {
        $content = file_get_contents('https://www.mockaroo.com/4c149340/download?count=1&key=3eecc1e0&type=json');

        $data = json_decode($content, true);

        return new JsonResponse($data);
    }
}
