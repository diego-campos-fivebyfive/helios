<?php

namespace AppBundle\Entity;

use AppBundle\Entity\BusinessInterface as SubscriberInterface;

interface NotificationSubscriberInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param NotificationInterface $notification
     * @return NotificationSubscriberInterface
     */
    public function setNotification(NotificationInterface $notification);

    /**
     * @return NotificationInterface
     */
    public function getNotification();

    /**
     * @param BusinessInterface $subscriber
     * @return NotificationSubscriberInterface
     */
    public function setSubscriber(SubscriberInterface $subscriber);

    /**
     * @return SubscriberInterface
     */
    public function getSubscriber();

    /**
     * @param \DateTime $viewedAt
     * @return NotificationSubscriberInterface
     */
    public function setViewedAt(\DateTime $viewedAt);

    /**
     * @return \DateTime
     */
    public function getViewedAt();

    /**
     * @return bool
     */
    public function isViewed();

    /**
     * @return \DateTime
     */
    public function getAddedAt();
}