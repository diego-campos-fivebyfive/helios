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

use AppBundle\Entity\Order\Order;
use AppBundle\Entity\TimelineInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Util\WorkingDays;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class StatusChanger
 * This class provides a centralized service for manipulating budgets status,
 * as well as their associated behaviors.
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class StatusChanger
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * StatusChanger constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Change status and process associated behaviors
     *
     * @param Order $order
     * @param $status
     */
    public function change(Order $order, $status)
    {
        if(in_array($status, Order::getStatusList())) {

            $order->setStatus($status);

            switch ($order->getStatus()){
                case Order::STATUS_PENDING:
                    $this->onChangeToPending($order);
                    break;

                case Order::STATUS_VALIDATED:
                    $this->onChangeToValidated($order);
                    break;

                case Order::STATUS_APPROVED:
                    $this->onChangeToApproved($order);
                    break;

                case Order::STATUS_REJECTED:
                    $this->onChangeToRejected($order);
                    break;
            }

            $this->finishStatusChanged($order);
        }
    }

    /**
     * Check if next status is accepted
     *
     * @param Order $order
     * @param $status
     * @param UserInterface $user
     * @return bool
     */
    public function accept(Order $order, $status, UserInterface $user)
    {
        return StatusChecker::acceptStatus($order->getStatus(), $status, $user->getType(), $user->getRoles());
    }

    /**
     * @param Order $order
     */
    private function finishStatusChanged(Order $order)
    {
        // TODO: Liberar linha após execução da tarefa 1037
        //$this->updateExpireAt($order);
        $this->createTimeline($order);
        $this->sendEmail($order);

        $this->get('order_manager')->save($order);
    }

    /**
     * @param Order $order
     */
    private function onChangeToPending(Order $order)
    {
        $order->setSendAt(new \DateTime('now'));

        if(!$order->getChildrens()->isEmpty())
            $order->setMetadata($order->getChildrens()->first()->getMetadata());

        $this->get('order_reference')->generate($order);
    }

    /**
     * @param Order $order
     */
    private function onChangeToValidated(Order $order)
    {
        if(!$order->getReference())
            $this->get('order_reference')->generate($order);
    }

    /**
     * @param Order $order
     */
    private function onChangeToApproved(Order $order)
    {
        if(Order::STATUS_DONE != $order->getPreviousStatus()){
            $this->get('order_exporter')->export($order);
            $this->get('order_stock')->debit($order);
            $this->generateProforma($order);
        }
    }

    /**
     * @param Order $order
     */
    private function onChangeToRejected(Order $order)
    {
        if(Order::STATUS_APPROVED == $order->getPreviousStatus())
            $this->get('order_stock')->credit($order);
    }

    /**
     * @param Order $order
     */
    private function updateExpireAt(Order $order)
    {
        /** @var \AppBundle\Entity\Parameter $parameter */
        $parameter = $this->get('parameter_manager')->findOrCreate('platform_settings');

        $expirationDays = (array) $parameter->get('order_expiration_days');

        if(array_key_exists($order->getStatus(), $expirationDays)){

            $days = (int) $expirationDays[$order->getStatus()]['days'];
            $note = $expirationDays[$order->getStatus()]['note'];

            if($days > 0){

                $expireAt = WorkingDays::create(new \DateTime())->next($days);

                $order->setExpireAt($expireAt);
                $order->setExpireNote($note);
            }
        }
    }

    /**
     * @param Order $order
     */
    private function createTimeline(Order $order)
    {
        $tag = $order->isApproved() && Order::STATUS_DONE == $order->getPreviousStatus()
            ? TimelineInterface::TAG_RETURNING_STATUS
            : TimelineInterface::TAG_STATUS;

        $this->get('order_timeline')->create($order, $tag);
    }

    /**
     * @param Order $order
     */
    private function sendEmail(Order $order)
    {
        $previous = $order->getPreviousStatus();

        $doneToApproved = $previous == Order::STATUS_DONE && $order->isApproved();
        $validatedToPending = $previous == Order::STATUS_VALIDATED && $order->isPending();

        if (!$doneToApproved && !$validatedToPending)
            $this->get('order_mailer')->sendOrderMessage($order);
    }

    /**
     * @param Order $order
     */
    private function generateProforma(Order $order)
    {
        /** @var \AppBundle\Service\Component\FileHandler $storage */
        $storage = $this->get('app_storage');
        /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router */
        $router = $this->get('router');

        $id = $order->getId();
        $date = (new \DateTime())->format('Ymd-His');
        $filename = sprintf('proforma_%s_%s_.pdf', $order->getId(), $date);

        $url = $router->generate('proforma_pdf', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);

        $options = [
            'id' => $id,
            'root' => 'order',
            'type' => 'proforma',
            'filename' => $filename,
            'access' => 'private',
            'snappy' => $url
        ];

        $file = $storage->location($options);

        $this->get('app_generator')->pdf($options, $file);

        if (file_exists($file)) {

            $storage->push($options, $file);

            $order->setProforma($filename);
        }
    }

    /**
     * @param $service
     * @return object
     */
    private function get($service)
    {
        return $this->container->get($service);
    }
}
