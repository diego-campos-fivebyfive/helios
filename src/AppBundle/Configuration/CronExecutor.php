<?php

namespace AppBundle\Configuration;

use GuzzleHttp\Client;

/**
 * Class CronExecutor
 */
class CronExecutor
{
    /**
     * @var array
     */
    private $environments = [

        'sandbox' => [
            'enabled' => true,
            'host' => 'https://sandbox.inovadorsolar.com',
            'token' => '98ZkOgnrBjlX_T9qawFH6bDAA_UfJgqRLQ'
        ],

        'app' => [
            'enabled' => true,
            'host' => 'https://app.inovadorsolar.com',
            'token' => 'WdG1eVppt0DlS_xhpIOaO6uhZDlm9PFimQ'
        ],

        'dev' => [
            'enabled' => true,
            'host' => 'http://localhost:8000',
            'token' => 'ciH7em6s115M3h8TIzu6bC5LunUpBODzxQ'
        ]
    ];

    /**
     * @var
     */
    private $environment;

    /**
     * @var
     */
    private $host;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $formParams = [];

    /**
     * @inheritDoc
     */
    public function __construct($environment)
    {
        if(!array_key_exists($environment, $this->environments)){
            $this->errors[] = sprintf('Invalid %s environment', $environment);
        }

        if(!$this->environments[$environment]['enabled']){
            $this->errors[] = sprintf('The environment %s is disabled', $environment);
        }

        $this->environment = $environment;
        $this->enabled = $this->environments[$environment]['enabled'];
        $this->host = $this->environments[$environment]['host'];
        $this->formParams = [
            'env' => $environment,
            'token' => $this->environments[$environment]['token']
        ];
    }

    /**
     * @return string
     */
    public function checkBills()
    {
        if(!$this->enabled)
            return 'disabled_service';

        $client = $this->createClient();

        $response = $client->request('post', '/public/webhooks/check_bills');

        return $response->getBody()->getContents();
    }

    /**
     * @return string
     */
    public function resetProjectsCount()
    {
        if(!$this->enabled)
            return 'disabled_service';

        $client = $this->createClient();

        $response = $client->request('post', '/public/webhooks/reset_projects_count');

        return $response->getBody()->getContents();
    }

    /**
     * @return int
     */
    public function hasErrors()
    {
        return count($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return Client
     */
    private function createClient()
    {
        return new Client([
            'base_uri' => $this->host,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'form_params' => $this->formParams
        ]);
    }
}