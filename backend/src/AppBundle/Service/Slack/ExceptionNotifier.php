<?php

namespace AppBundle\Service\Slack;

use GuzzleHttp\Client;

/**
 * Send exception notifications to slack
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class ExceptionNotifier
{
    /**
     * @var string
     */
    private $slackUri = 'https://hooks.slack.com';

    /**
     * @var string
     */
    private $slackEntry = '/services/T637J8WTD';

    /**
     * @var array
     */
    private $slackChannels = [
        'tester' => '/B7RG9C6QY/83hdQY97qEOsPjYWexEfobCN',
        'backend' => '/B9PBEM4CX/pgEmHlA5upMTLwwrk0COqbbI',
        'developers' => '/B9NDJ4FCM/eNlfu3KYJTzUxE9grjlfcytb',
        'frontend' => '/B9ML39XG8/3GMotrj2XPBQzBazlwfSqjuZ',
        'homolog' => '/B7PPVC42D/FIXd946YfYB6wN5XBIE9P5yR',
        'production' => '/B7PKYSBEC/R2k2c5GqdzjtazDFuabywnmG',
        'tasks' => '/B84MS6VNW/t2T7U7t6y1LKVRZHvOZCxPY0'
    ];

    /**
     * @var string
     */
    private $environment;

    /**
     * ExceptionNotifier constructor.
     * @param $environment
     */
    function __construct($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param \Exception $exception
     * @return int
     */
    public function notify($exception)
    {
        $output = $this->formatOutput($exception);
        $uri = $this->formatUri();
        $client = $this->createClient();

        $data = [
            'text' => $output,
            'link_names' => 1
        ];

        $response = $client->post($uri, [
            'body' => stripslashes(json_encode($data, JSON_UNESCAPED_SLASHES))
        ]);

        return $response->getStatusCode();
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    private function formatOutput($exception)
    {
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 0 ;
        $message = addslashes($exception->getMessage());
        $file = $exception->getFile();
        $line = $exception->getLine();

        $output = sprintf('\n\nException on %s ambience...\n', $this->environment);
        $output .= ':: INFO \n';
        $output .= sprintf('Message: %s \n', $message);
        $output .= sprintf('Status: %d \n', $statusCode);
        $output .= sprintf('File: %s \n', $file);
        $output .= sprintf('Line: %s \n', $line);
        $output .= '::TRACE \n';

        $traced = $this->formatTraceOutput($exception->getTrace());

        $output .= strlen($traced) ? $traced : 'No trace info.';
        $output .= '\n\n';

        return $output;
    }

    /**
     * @param array $trace
     * @return string
     */
    private function formatTraceOutput(array $trace)
    {
        $output = '';
        foreach ($trace as $invocked) {

            $info = '';
            foreach ($invocked as $definition => $value) {

                if ('class' == $definition) {
                    $namespace = explode('\\', $value)[0];
                    if (!in_array($namespace, ['App', 'AppBundle', 'AdminBundle', 'ApiBundle'])) {
                        break 2;
                    }
                }

                if ($value && $definition != 'type' && !is_array($value)) {

                    $tag = ucfirst($definition);
                    $str = addslashes(is_string($value) ? $value : '');

                    if ($str) {
                        $info .= sprintf('%s: %s\n', $tag, $str);
                    }
                }
            }

            if (strlen($info)) {
                $output .= sprintf('%s\n', $info);
            }
        }

        return $output;
    }

    /**
     * @return Client
     */
    private function createClient()
    {
        return new Client([
            'base_uri' => $this->slackUri,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * @return string
     */
    private function formatUri()
    {
        return "{$this->slackEntry}{$this->slackChannels[$this->environment]}";
    }
}
