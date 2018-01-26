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

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\OrderInterface;
use Fos\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Mailer
 * @package AppBundle\Service\Mailing
 */
class Mailer extends AbstractMailer
{
    /**
     * @var bool
     */
    public $enableSender = true;

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

        $rendered = $this->templating->render('AppBundle:Register:email_confirmation.html.twig', [
            'targetUrl' => $url,
            'account' => $account
        ]);

        $this->sendEmailMessage('Plataforma Sices Solar - Cadastro', $rendered, self::FROM_EMAIL, $account->getEmail());

        return $rendered;
    }

    /**
     * @param AccountInterface $register
     * @return string
     */
    public function sendAccountVerifyMessage(AccountInterface $account)
    {
        $url = $this->router->generate('app_register_verify',[
            'token' => $account->getConfirmationToken(),
            'reference' => base64_encode($account->getEmail())
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->templating->render('AppBundle:Register:email.html.twig', [
            'targetUrl' => $url,
            'account' => $account
        ]);

        $this->sendEmailMessage('Plataforma Sices Solar - Verificação', $rendered, self::FROM_EMAIL, $account->getEmail());

        return $rendered;
    }

    /**
     * @param UserInterface $user
     * @return string
     */
    public function sendEmailResettingMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_resetting_reset',['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $rendered = $this->templating->render('FOSUserBundle:Resetting:email.html.twig', [
            'targetUrl' => $url,
            'user' => $user
        ]);

        $this->sendEmailMessage('Plataforma Sices Solar - Redefinir Senha', $rendered, self::FROM_EMAIL, $user->getEmail());

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
     * @param OrderInterface $order
     */
    public function sendOrderMessage(OrderInterface $order)
    {
        $message = $this->createMessageOrder($order);

        if ($message instanceof \Swift_Message) {
            $this->sendMessage($message);
        }
    }

    /**
     * @param OrderInterface $order
     * @return \Swift_Message
     */
    private function createMessageOrder(OrderInterface $order)
    {
        $account = $order->getAccount();
        $parameters  = $this->getMessageParameters($order);

        $message = $this->createMessage($parameters);

        $this->resolvePlatformEmails($message);

        $this->resolveAccountEmails($message, $account);

        if(null != $agent = $account->getAgent()) {

            $agentEmail = $agent->getEmail();
            $agentName = $agent->getFirstname();

            $message
                ->addCc($agentEmail, $agentName)
                ->setReplyTo($agentEmail, $agentName);
        }

        $this->addExpanseCc($account, $message);

        return $message;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getMessageParameters(OrderInterface $order)
    {
        $message = $order->getMessages()->last();

        $parameters = [
            'subject' => 'Nova mensagem',
            'body' => $this->templating->render('orders/emails/message.html.twig', array(
                'message' => $message,
                'order' => $order
            ))
        ];

        return $parameters;
    }

    /**
     * @param $subject
     * @param $body
     * @param $fromEmail
     * @param $toEmail
     * @return int
     */
    private function sendEmailMessage($subject, $body, $fromEmail, $toEmail)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($fromEmail, 'Plataforma Sices Solar')
            ->setSubject($subject)
            ->setTo($toEmail)
            ->setBody($body)
            ->setContentType('text/html')
        ;

        return $this->sendMessage($message);
    }
}
