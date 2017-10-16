<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Order;

use AppBundle\Service\AbstractMailer;
use AppBundle\Entity\Order\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class OrderMailer
 * This class execute email send for orders
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class OrderMailer extends AbstractMailer
{
    /**
     * @var array
     */
    private $mapping = [
        OrderInterface::STATUS_PENDING => [
            'enabled' => true,
            'subject' => 'Plataforma SICES Solar - Orçamento Preliminar nº %s'
        ],
        OrderInterface::STATUS_VALIDATED => [
            'enabled' => true,
            'subject' => 'Plataforma SICES Solar - Orçamento Validado nº %s'
        ],
        OrderInterface::STATUS_APPROVED => [
            'enabled' => true,
            'subject' => 'Plataforma SICES Solar - Orçamento Aprovado nº %s'
        ],
        OrderInterface::STATUS_REJECTED => [
            'enabled' => false,
            'subject' => 'Plataforma SICES Solar - Orçamento Rejeitado nº %s'
        ],
        OrderInterface::STATUS_DONE => [
            'enabled' => false,
            'subject' => 'Plataforma SICES Solar - Orçamento Concluído nº %s'
        ]
    ];

    /**
     * @param OrderInterface $order
     */
    public function sendOrderMessage(OrderInterface $order)
    {
        if($this->ensureOrder($order)) {

            $message = $this->createOrderMessage($order);

            if ($message instanceof \Swift_Message) {
                $this->sendMessage($message);
            }
        }
    }

    /**
     * @param OrderInterface $order
     * @return \Swift_Message
     */
    private function createOrderMessage(OrderInterface $order)
    {
        $account = $order->getAccount();
        $owner = $account->getOwner();
        $parameters  = $this->getMessageParameters($order);

        $message = $this->createMessage($parameters);

        $this->resolvePlatformEmails($message);

        $message->setTo($owner->getEmail(), $owner->getName());

        if(null != $agent = $account->getAgent()) {

            $agentEmail = $agent->getEmail();
            $agentName = $agent->getFirstname();

            $message
                ->addCc($agentEmail, $agentName)
                ->setReplyTo($agentEmail, $agentName);
        }

        if($order->isApproved() && $order->getProforma()){
            $message->attach($this->createOrderAttachment($order));
        }

        return $message;
    }

    /**
     * @param OrderInterface $order
     * @return \Swift_Mime_Attachment
     */
    private function createOrderAttachment(OrderInterface $order)
    {
        $options = array(
            'filename' => $order->getProforma(),
            'root' => 'order',
            'type' => 'proforma'
        );

        $file = $this->container->get('app_storage')->location($options);

        return $this->createAttachment($file);
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getMessageParameters(OrderInterface $order)
    {
        $config = $this->mapping[$order->getStatus()];

        $parameters = [
            'subject' => sprintf($config['subject'], $order->getReference()),
            'body' => $this->createBody($order)
        ];

        return $parameters;
    }

    /**
     * @param OrderInterface $order
     * @return string
     */
    private function createBody(OrderInterface $order)
    {
        $content = $this->render(sprintf('orders/emails/%s.html.twig', self::statusToText($order->getStatus())), [
            'order' => $order
        ]);

        return $content;
    }

    /**
     * @param OrderInterface $order
     */
    private function ensureOrder(OrderInterface $order)
    {
        if(!$order->isBudget())
            self::exception('This order is not master');

        if(array_key_exists($order->getStatus(), $this->mapping)){
            return $this->mapping[$order->getStatus()]['enabled'];
        }

        return false;
    }

    /**
     * @param \Swift_Message $message
     */
    private function resolvePlatformEmails(\Swift_Message $message)
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
     * @return array
     */
    private function getPlatformSettings()
    {
        $manager = $this->manager('parameter');

        $settings = $manager->findOrCreate('platform_settings')->all();

        return $settings;
    }

    /**
     * @param $status
     * @return string
     */
    private static function statusToText($status)
    {
        $statuses = ['building', 'pending', 'validated', 'approved', 'rejected', 'done'];

        return $statuses[$status];
    }

    /**
     * Generate exception
     * @param $message
     */
    private static function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
