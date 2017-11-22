<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\TimelineInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Manager\TimelineManager;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrderTimeline
{
    /**
     * @var TimelineManager
     */
    private $manager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * OrderTimeline constructor.
     * @param TimelineManager $manager
     * @param TokenStorageInterface $tokenStorage
     */
    function __construct(TimelineManager $manager, TokenStorageInterface $tokenStorage)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->getUser();
    }

    /**
     * @param OrderInterface $order
     * @return mixed|object
     */
    public function create(OrderInterface $order, $tag = TimelineInterface::TAG_STATUS)
    {
        $timeline = $this->manager->create();

        $timeline->setTarget(sprintf('%s::%s', self::getClass($order), $order->getId()))
            ->setMessage(self::loadMessage($order, $tag))
            ->addAttribute('status', $order->getStatus())
            ->addAttribute('statusLabel', self::loadStatusLabel($order))
            ->setCreatedAt(new \DateTime());

        $this->manager->save($timeline);

        return $timeline;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function load(OrderInterface $order)
    {
        return array_reverse($this->manager->findBy([
            'target' => sprintf('%s::%s', self::getClass($order), $order->getId())
        ]));
    }

    /**
     * @param $object
     * @return string
     */
    public static function getClass($object)
    {
        return ClassUtils::getClass($object);
    }

    /**
     * @return BusinessInterface
     */
    private function getMember()
    {
        return $this->user->getInfo();
    }

    /**
     * @return \AppBundle\Entity\UserInterface
     */
    private function getUser()
    {
        $this->user = $this->tokenStorage->getToken()->getUser();

        if(!$this->user || 'anon.' == $this->user)
            $this->denyAccess();
    }

    /**
     * Common Deny Access
     */
    private function denyAccess()
    {
        throw new AccessDeniedException();
    }

    /**
     * @param $order
     * @return string
     */
    private function loadMessage(OrderInterface $order, $tag)
    {
        $status = $order->getStatus();
        $status = $status == OrderInterface::STATUS_BUILDING && !count(self::load($order))
            ? 'initiated' : $status;

        switch ($tag) {
            case TimelineInterface::TAG_FILE_PAYMENT:
                $status = 'filePayment';
                break;

            case TimelineInterface::TAG_DELIVERY_ADDRESS:
                $status = 'deliveryAddress';
                break;
        }

        $messages = [
            'filePayment' => 'adicionou/alterou comprovante de pagamento.',
            'deliveryAddress' => 'adicionou/alterou endereço de entrega.',
            'initiated' => 'criou o orçamento.',
            OrderInterface::STATUS_BUILDING => 'editou o orçamento.',
            OrderInterface::STATUS_PENDING => $tag == TimelineInterface::TAG_RETURNING_STATUS ? 'alterou o orçamento.' : 'enviou solicitação para SICES.',
            OrderInterface::STATUS_VALIDATED => 'validou o orçamento.',
            OrderInterface::STATUS_APPROVED => $tag == TimelineInterface::TAG_RETURNING_STATUS ? 'cancelou confirmação de pagamento.' : 'aprovou o orçamento.',
            OrderInterface::STATUS_REJECTED => 'cancelou o orçamento.',
            OrderInterface::STATUS_DONE => 'confirmou pagamento conforme pró-forma.',
            OrderInterface::STATUS_INSERTED => ': início da produção',
            OrderInterface::STATUS_AVAILABLE => ': disponível para coleta.',
            OrderInterface::STATUS_COLLECTED => ': coleta realizada.',
            OrderInterface::STATUS_BILLED => ': pedido faturado.',
            OrderInterface::STATUS_DELIVERED => ': entrega realizada.'
        ];

        return sprintf('%s %s', $this->getMember()->getFirstname(), $messages[$status]);
    }

    /**
     * @param $order
     * @return string
     */
    private function loadStatusLabel(OrderInterface $order)
    {
        return !count(self::load($order)) && $order->getStatus() == OrderInterface::STATUS_BUILDING
            ? 'initiated' : Order::getStatusNames()[$order->getStatus()];
    }
}
