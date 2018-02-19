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

use AppBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractMailer
 * This class provide a default mechanism for send e-mails
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * AbstractMailer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->router = $container->get('router');
        $this->templating = $container->get('templating');
        $this->container = $container;
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
        $message = \Swift_Message::newInstance();

        $message
            ->setContentType('text/html')
            ->setFrom([self::FROM_EMAIL => self::FROM_NAME])
        ;

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

    /**
     * @param $id
     * @return object|\AppBundle\Manager\AbstractManager
     */
    protected function manager($id)
    {
        return $this->container->get(sprintf('%s_manager', $id));
    }

    /**
     * @param \Swift_Message $message
     */
    protected function resolvePlatformEmails(\Swift_Message $message)
    {
        $settings = $this->getPlatformSettings();

        $addIfDefined = function($target, $bcc = false) use($settings, $message){
            if(array_key_exists($target, $settings) && !empty($settings[$target]['email'])){
                if($bcc){
                    $message->addBcc($settings[$target]['email'], $settings[$target]['name']);
                }else {
                    $message->addCc($settings[$target]['email'], $settings[$target]['name']);
                }
            }
        };

        $addIfDefined('admin');
        $addIfDefined('master', true);
    }

    /**
     * @param \Swift_Message $message
     * @param $account
     */
    protected function resolveAccountEmails(\Swift_Message $message, Customer $account)
    {
        foreach ($account->getOwners() as $owner) {
            if ($owner->isActivated()) {
                $message->addTo($owner->getEmail(), $owner->getName());
            }
        }
    }

    /**
     * @param $account
     * @param $message
     */
    protected function addExpanseCc(Customer $account, $message)
    {
        $state = $account->getState();

        $qb = $this->manager('member')->createQueryBuilder();

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('c.context', ':member'),
                $qb->expr()->like('c.attributes',
                    $qb->expr()->literal('%"'.$state.'"%')
                ),
                $qb->expr()->eq('c.status', ':status')
            )
        );

        $qb->setParameters([
            'member' => 'member',
            'status' => Customer::ACTIVATED
        ]);

        $members = $qb->getQuery()->getResult();

        /** @var MemberInterface $member */
        foreach ($members as  $member) {
            if($member->isPlatformExpanse()) {

                $expanseEmail = $member->getEmail();
                $expanseName = $member->getName();

                $message->addCc($expanseEmail, $expanseName);
            }
        }
    }

    /**
     * @return array
     */
    private function getPlatformSettings()
    {
        $manager = $this->manager('parameter');

        $settings = $manager->findOrCreate('platform_settings')->all();

        return $settings;
    }
}
