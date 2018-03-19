<?php

namespace AppBundle\Service\Postcode;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\Client as Guzzle;


class Finder
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Finder constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function searchAndFormat(Request $request)
    {
        $postcode = $request->request->get('postcode');

        $uri = "https://viacep.com.br/ws/{$postcode}/json/";

        $client = new Guzzle([
            'base_uri' => $uri,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        try {
            $result = $client->request('get');

            $status = $result->getStatusCode();

            $resultData = json_decode($result->getBody()->getContents(), true);

            if (!isset($resultData['erro'])) {

                $response = [
                    'status' => $status,
                    'state' => $resultData['uf'],
                    'city' => $resultData['localidade'],
                    'neighborhood' => $resultData['bairro'],
                    'street' => $resultData['logradouro']
                ];
            } else {
                $response = [
                    'status' => 404
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'status' => 404
            ];
        }

        return $response;
    }
}
