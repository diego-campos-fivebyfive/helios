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
    private $options = [];

    /**
     * OrderMailer constructor.
     * @param UrlGeneratorInterface $router
     * @param EngineInterface $templating
     * @param array $options
     */
    function __construct(UrlGeneratorInterface $router, EngineInterface $templating, array $options = [])
    {
        parent::__construct($router, $templating);

        $this->options = $options;
    }

    /**
     * @var array
     */
    private $config = [
        OrderInterface::STATUS_PENDING => [
            'subject' => '[Edit this subject] - Pending'
        ],
        OrderInterface::STATUS_VALIDATED => [
            'subject' => '[Edit this subject] - Validated'
        ],
        OrderInterface::STATUS_APPROVED => [
            'subject' => '[Edit this subject] - Approved'
        ],
        OrderInterface::STATUS_REJECTED => [
            'subject' => '[Edit this subject] - Rejected'
        ]
    ];

    /**
     * @param OrderInterface $order
     */
    public function sendOrderMessage(OrderInterface $order)
    {
        $this->ensureOrder($order);

        $message = $this->createOrderMessage($order);

        if($message instanceof \Swift_Message){
            $this->sendMessage($message);
        }
    }

    /**
     * @param OrderInterface $order
     * @return \Swift_Message
     */
    private function createOrderMessage(OrderInterface $order)
    {
        $parameters  = $this->getMessageParameters($order);

        $message = $this->createMessage($parameters);

        if($order->getStatus() === OrderInterface::STATUS_APPROVED){
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
        // TODO: Change 'proforma.pdf' to $order->getFile();
        $filename = 'proforma.pdf';
        $storage = $this->options['storage'];

        $path = $storage . $filename;

        return $this->createAttachment($path);
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getMessageParameters(OrderInterface $order)
    {
        $config = $this->config[$order->getStatus()];
        $account = $order->getAccount();

        $parameters = [
            'subject' => $config['subject'],
            'to' => $account->getEmail(),
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
        if(!array_key_exists($order->getStatus(), $this->config))
            self::exception('Incompatible order status.');

        if(!$order->isBudget())
            self::exception('This order is not master');

        if(OrderInterface::STATUS_BUILDING == $order->getStatus())
            self::exception(sprintf('Incompatible order status: [%s]', self::statusToText($order->getStatus())));
    }

    /**
     * @param $status
     * @return string
     */
    private static function statusToText($status)
    {
        $statuses = ['building', 'pending', 'validated', 'approved', 'rejected'];

        return $statuses[$status];
    }

    private static function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
