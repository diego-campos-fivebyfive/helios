<?php

namespace AppBundle\Service;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Extra\AccountRegister;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Mailer
 * @package AppBundle\Service\Mailing
 */
class Mailer
{
    const FROM_EMAIL = 'no-reply@inovadorsolar.com';

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
     * @param BusinessInterface $member
     * @return string
     */
    public function sendUnprocessedPaymentRenewSignature(BusinessInterface $member)
    {
        $rendered = $this->templating->render('AppBundle:Signature:email_payment.html.twig', [
            'member' => $member
        ]);

        $this->sendEmailMessage('Inovador Solar - Renovação', $rendered, self::FROM_EMAIL, $member->getEmail());

        return $rendered;
    }

    /**
     * @param AccountRegister $register
     * @return string
     */
    public function sendAccountConfirmationMessage(AccountRegister $register)
    {
        $url = $this->router->generate('app_register_confirm',[
            'token' => $register->getConfirmationToken(),
            'reference' => base64_encode($register->getEmail())
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->templating->render('AppBundle:Register:email.html.twig', [
            'targetUrl' => $url,
            'register' => $register
        ]);

        $this->sendEmailMessage('Inovador Solar - Registro', $rendered, self::FROM_EMAIL, $register->getEmail());

        return $rendered;
    }

    /**
     * @param BusinessInterface $member
     * @return string
     */
    public function sendMemberConfirmationMessage(BusinessInterface $member)
    {
        $url = $this->router->generate('app_user_confirm', ['token' =>  $member->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->templating->render('AppBundle:Register:email.html.twig', [
            'targetUrl' => $url,
            'member' => $member
        ]);

        $this->sendEmailMessage('Inovador Solar - Convite', $rendered, self::FROM_EMAIL, $member->getEmail());

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
                ->setFrom($fromEmail, 'Inovador Solar')
                ->setTo($toEmail)
                ->setContentType('text/html')
                ->setBody($body);

            $this->mailer->send($message);
        }
    }
}