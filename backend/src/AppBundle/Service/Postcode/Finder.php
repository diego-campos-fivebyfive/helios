<?php

namespace AppBundle\Service\Postcode;

use AppBundle\Entity\Postcode;
use AppBundle\Manager\PostcodeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @param $postcode
     * @return array
     */
    public function find($postcode)
    {
        $result = $this->searchDB($postcode);

        if ($result) {
            $resultData = $result->getAttributes();
            $resultData['status'] = 200;
        } else {
            $resultData = $this->searchWS($postcode);

            if ($resultData['status'] == 200) {
                $this->saveDB([
                    'postcode' => $resultData['postcode'],
                    'state' => $resultData['state'],
                    'city' => $resultData['city'],
                    'neighborhood' => $resultData['neighborhood'],
                    'street' => $resultData['street']
                ]);
            }
        }

        return $resultData;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function searchWS($postcode)
    {
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
                    'postcode' => $resultData['cep'],
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

    /**
     * @param $postcode
     * @return null|object
     */
    private function searchDB($postcode)
    {
        /** @var PostcodeManager $postcodeManager */
        $postcodeManager = $this->container->get('postcode_manager');

        $postcode = $postcodeManager->findOneBy([
            'id' => str_replace("-", "", $postcode)
        ]);

        return $postcode;
    }

    /**
     * @param $postcodeData
     */
    private function saveDB($postcodeData)
    {
        /** @var PostcodeManager $postcodeManager */
        $postcodeManager = $this->container->get('postcode_manager');

        /** @var Postcode $postcode */
        $postcode = $postcodeManager->create();

        $postcode->setId($postcodeData['postcode']);
        $postcode->setAttributes($postcodeData);

        $postcodeManager->save($postcode);
    }
}
