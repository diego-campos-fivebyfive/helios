<?php

namespace AppBundle\Controller\PublicAccess;

use GuzzleHttp\Client as Guzzle;
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

        $uri = sprintf('//cep.republicavirtual.com.br/web_cep.php?cep=%s&formato=json', $postcode);

        $client = new Guzzle([
            'base_uri' => $uri,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $response = $client->request('post');

        return $this->json(json_decode($response->getBody()->getContents(), true));
    }
}