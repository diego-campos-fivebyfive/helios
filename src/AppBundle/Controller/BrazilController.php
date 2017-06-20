<?php

namespace AppBundle\Controller;

use AppBundle\Configuration\Brazil;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\Client as Guzzle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("brazil")
 */
class BrazilController extends Controller
{
    /**
     * @Route("/{state}/cities", name="brazil_cities")
     */
    public function citiesAction($state)
    {
        return new JsonResponse([
            'cities' => Brazil::cities($state)
        ]);
    }

    /**
     * @Route("/postcode", name="brazil_postcode")
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

        return new JsonResponse(json_decode($response->getBody()->getContents()));
    }
}
