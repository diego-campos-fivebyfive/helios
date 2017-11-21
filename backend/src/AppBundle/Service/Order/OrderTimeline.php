<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderInterface;
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
    public function create(OrderInterface $order)
    {
        $timeline = $this->manager->create();

        $timeline->setTarget(sprintf('%s::%s', self::getClass($order), $order->getId()))
            ->setMessage(self::loadMessage($order))
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
        return $this->manager->findBy([
            'target' => sprintf('%s::%s', self::getClass($order), $order->getId())
        ]);
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
    private function loadMessage(OrderInterface $order)
    {
        $status = $order->getStatus();
        $status = $status != OrderInterface::STATUS_BUILDING && $status != OrderInterface::STATUS_REJECTED
        || $status == OrderInterface::STATUS_BUILDING && $order->getStatusAt() > $order->getCreatedAt()
            ? 'other' : $status;

        $messages[OrderInterface::STATUS_BUILDING] = 'iniciou o orçamento.';
        $messages[OrderInterface::STATUS_REJECTED] = 'rejeitou o orçamento.';
        $messages['other'] = 'alterou o status do orçamento.';

        return sprintf('%s %s', $this->getMember()->getFirstname(), $messages[$status]);
    }

    /**
     * @param $order
     * @return string
     */
    private function loadStatusLabel(OrderInterface $order)
    {
        return $order->getStatusAt() <= $order->getCreatedAt()
        && $order->getStatus() == OrderInterface::STATUS_BUILDING
            ? 'initiated' : Order::getStatusNames()[$order->getStatus()];
    }
}
