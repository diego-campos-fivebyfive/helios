<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Checkout;

use GuzzleHttp\Client;

class Getnet
{
    const PRODUCTION = 1;
    const HOMOLOG = 0;

    /**
     * @var string
     */
    private $env;

    /**
     * @var array
     */
    private $config = [
        self::HOMOLOG => [
            'base_uri' => 'https://api-homologacao.getnet.com.br',
            'auth_uri' => '/auth/oauth/v2/token',
            'client_id' => '45ee91a9-f7c5-4bac-96d0-40e5fdcecda3',
            'client_secret' => 'e845fbfa-7679-44bc-9ed3-7d1e5f34bf5e',
            'seller_id' => '4609a349-e4b8-4edf-a332-5a45c9fe7d19'
        ],
        self::PRODUCTION => [
            'base_uri' => '...waiting-for-homolog-config',
            'auth_uri' => '/auth/oauth/v2/token',
            'client_id' => '',
            'client_secret' => '',
            'seller_id' => ''
        ]
    ];

    /**
     * @var string
     */
    private $authorizationHeader;

    /**
     * @var Client
     */
    private $client;

    /**
     * Getnet constructor.
     * @param string $env
     * @throws \Exception
     */
    function __construct(string $env)
    {
        if(!isset($this->config[$env])){
            throw new \Exception(sprintf('Invalid env %s', $env));
        }

        $this->env = $env;

        $this->buildAuthorizationHeader();

        $this->buildClient();
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        $uri = $this->config[$this->env]['auth_uri'];

        $response = $this->client->post($uri);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['access_token'];
    }

    /**
     * @return string
     */
    public function getAuthorizationHeader()
    {
        return $this->authorizationHeader;
    }

    /**
     * Build default authorization header
     */
    private function buildAuthorizationHeader()
    {
        $id = $this->config[$this->env]['client_id'];
        $secret = $this->config[$this->env]['client_secret'];

        $this->authorizationHeader = sprintf('Basic %s', base64_encode(sprintf('%s:%s', $id, $secret)));
    }

    /**
     * Build Guzzle\Client
     */
    private function buildClient()
    {
        $this->client = new Client([
            'base_uri' => $this->config[$this->env]['base_uri'],
            'headers' => [
                'Authorization' => $this->authorizationHeader
            ],
            'form_params' => [
                'scope' => 'oob',
                'grant_type' => 'client_credentials'
            ]
        ]);
    }
}