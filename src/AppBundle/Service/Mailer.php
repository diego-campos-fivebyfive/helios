<?php

namespace AppBundle\Service;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\MemberInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Mailer
 * @package AppBundle\Service\Mailing
 */
class Mailer
{
    const FROM_EMAIL = 'naoresponder@plataformasicessolar.com.br';

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var bool
     */
    public $enableSender = true;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param UrlGeneratorInterface $router
     * @param EngineInterface $templating
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface  $router, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
    }

    /**
     * @param AccountInterface $register
     * @return string
     */
    public function sendAccountConfirmationMessage(AccountInterface $account)
    {
        $url = $this->router->generate('app_register_confirm',[
            'token' => $account->getConfirmationToken(),
            'reference' => base64_encode($account->getEmail())
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->templating->render('AppBundle:Register:email.html.twig', [
            'targetUrl' => $url,
            'account' => $account
        ]);

        $this->sendEmailMessage('Plataforma Sices Solar - Registro', $rendered, self::FROM_EMAIL, $account->getEmail());

        return $rendered;
    }

    /**
     * @param MemberInterface $member
     * @return string
     */
    public function sendMemberConfirmationMessage(MemberInterface $member)
    {
        $user = $member->getUser();

        $url = $this->router->generate('app_user_confirm', ['token' =>  $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->templating->render('AppBundle:Register:email.html.twig', [
            'targetUrl' => $url,
            'member' => $member
        ]);

        $this->sendEmailMessage('Plataforma Sices Solar - Convite', $rendered, self::FROM_EMAIL, $member->getEmail());

        return $rendered;
    }

    /**
     * @param $subject
     * @param $body
     * @param $fromEmail
     * @param $toEmail
     */
    protected function sendEmailMessage($subject, $body, $fromEmail, $toEmail)
    {
        if($this->enableSender) {

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($fromEmail, 'Plataforma Sices Solar')
                ->setTo($toEmail)
                ->setContentType('text/html')
                ->setBody($body);

            $this->mailer->send($message);
        }
    }
}