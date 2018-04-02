<?php

namespace AppBundle\Service\Slack;

use AppBundle\Entity\User;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
        'tasks' => '/B84MS6VNW/t2T7U7t6y1LKVRZHvOZCxPY0',
        'development' => '/B7PPVC42D/FIXd946YfYB6wN5XBIE9P5yR'
    ];

    /**
     * @var array
     */
    private $namespaces = ['App', 'AppBundle', 'AdminBundle', 'ApiBundle'];

    /**
     * @var string
     */
    private $environment;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $ignoreNotifications = [403, 404];

    /**
     * ExceptionNotifier constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->environment = $container->getParameter('ambience');
        $this->request = $container->get('request_stack')->getCurrentRequest();
        $this->serializer = $container->get('serializer');
    }

    /**
     * @param \Exception $exception
     * @return int
     */
    public function notify($exception)
    {
        if (!in_array($exception->getStatusCode(), $this->ignoreNotifications)) {

            $output = $this->formatOutput($exception);
            $client = $this->createClient();

            $response = $client->post($this->formatUri(), [
                'body' => $this->formatBody($output)
            ]);

            return $response->getStatusCode();
        }
    }

    /**
     * @param $output
     * @return string
     */
    private function formatBody($output)
    {
        $data = [
            'text' => $output,
            'link_names' => 1
        ];

        return stripslashes(json_encode($data, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    private function formatOutput($exception)
    {
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 0;
        $message = addslashes($exception->getMessage());
        $file = $exception->getFile();
        $line = $exception->getLine();

        $output = sprintf('\nException on %s ambience...\n', $this->environment);
        $output .= '*ERROR* \n';
        $output .= sprintf('Message: %s \n', $message);
        $output .= sprintf('Status: %d \n', $statusCode);
        $output .= sprintf('File: %s \n', $file);
        $output .= sprintf('Line: %s \n', $line);

        $output .= '*INFO*';
        $output .= $this->extractRequestInfo();
        $output .= '\n';

        $traced = $this->formatTraceOutput($exception->getTrace());
        $output .= strlen($traced) ? '*TRACE* \n' . $traced : '';

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
            if ($this->isInvokedTraced($invocked)) {

                foreach ($invocked as $definition => $value) {

                    if (in_array($definition, ['file', 'class', 'line', 'function'])) {
                        $info .= sprintf('_%s:_ %s\n', ucfirst($definition), addslashes($value));
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

    /**
     * @return string
     */
    private function extractRequestInfo()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $info = '';
        if($user instanceof User){

            $member = $user->getInfo();
            $account = $member->getAccount();

            $info .= sprintf('\n_account: (%s) %s', $account->getId(), $account->getFirstname());
            $info .= sprintf('\n_user: (%s) %s', $member->getId(), $member->getFirstname());
        }

        $info .= $this->arrayToInfo($this->request->attributes->all());

        return $info;
    }

    /**
     * @param array $data
     * @param bool $inline
     * @return string
     */
    private function arrayToInfo(array $data = [], $inline = false)
    {
        $data = json_decode($this->serializer->serialize($data, 'json'), true);

        $info = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (!empty($value)) {
                    $info .= sprintf('\n%s: %s', $key, $this->arrayToInfo($value, true));
                }
            } else {
                $info .= sprintf('\n%s %s: %s', ($inline ? '>' : ''), $key, addslashes($value));
            }
        }

        return $info;
    }

    /**
     * @param array $invocked
     * @return bool
     */
    private function isInvokedTraced(array $invocked)
    {
        if (array_key_exists('file', $invocked)) {
            return $this->isNamespaceTraced($invocked['file']);
        }

        if (array_key_exists('class', $invocked)) {
            return $this->isNamespaceTraced($invocked['class']);
        }

        return false;
    }

    /**
     * @param $value
     * @return bool
     */
    private function isNamespaceTraced($value)
    {
        if (is_string($value)) {

            foreach ($this->namespaces as $namespace) {

                $dir = sprintf('/%s/', $namespace);
                $name = sprintf('\%s\\', $namespace);

                if (strpos($value, $dir) > 5 || 0 === strpos($value, $name)) {
                    return true;
                }
            }
        }

        return false;
    }
}
