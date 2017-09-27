<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractMailer
 * This class provide a default mechanism for send e-mails
 *
 * @package AppBundle\Service
 */
abstract class AbstractMailer
{
    const FROM_EMAIL = 'naoresponder@plataformasicessolar.com.br';
    const FROM_NAME = 'Plataforma Sices Solar';

    /**
     * @var array
     */
    private $config = [
        'host' => 'smtp.mail.eu-west-1.awsapps.com',
        'port' => 465,
        'encryption' => 'ssl',
        'username' => 'naoresponder@plataformasicessolar.com.br',
        'password' => 'Ze6}Kr6bWVky@z@Qsi'
    ];

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param UrlGeneratorInterface $router
     * @param EngineInterface $templating
     */
    public function __construct(UrlGeneratorInterface  $router, EngineInterface $templating)
    {
        $this->router = $router;
        $this->templating = $templating;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param \Swift_Message $message
     * @param null $fails
     * @return int
     */
    protected function sendMessage(\Swift_Message $message)
    {
        return $this->createMailer()->send($message, $this->errors);
    }

    /**
     * @return \Swift_SmtpTransport
     */
    protected function createTransport()
    {
        $transport = (new \Swift_SmtpTransport($this->config['host'], $this->config['port'], $this->config['encryption']))
            ->setUsername($this->config['username'])
            ->setPassword($this->config['password'])
        ;

        return $transport;
    }

    /**
     * @return \Swift_Mailer
     */
    protected function createMailer()
    {
        return new \Swift_Mailer($this->createTransport());
    }

    /**
     * @param array $parameters
     * @return \Swift_Message
     */
    protected function createMessage(array $parameters)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $message = new \Swift_Message();

        $message
            ->setContentType('text/html')
            ->setFrom(self::FROM_EMAIL, self::FROM_NAME);

        foreach ($parameters as $property => $value){
            $accessor->setValue($message, $property, $value);
        }

        return $message;
    }

    /**
     * @param $path
     * @return \Swift_Mime_Attachment
     */
    protected function createAttachment($path)
    {
        return \Swift_Attachment::fromPath($path);
    }

    /**
     * @param $view
     * @param array $parameters
     * @return string
     */
    protected function render($view, array $parameters = [])
    {
        return $this->templating->render($view, $parameters);
    }
}
