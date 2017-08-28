<?php

namespace AppBundle\Entity;

use AppBundle\Entity\BusinessInterface as SubscriberInterface;

interface NotificationInterface
{
    const TYPE_TIMELINE = 'timeline';
    const TYPE_DROPDOWN = 'dropdown';
    const TYPE_EMAIL    = 'email';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param $type
     * @return NotificationInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $icon
     * @return NotificationInterface
     */
    public function setIcon($icon);

    /**
     * @return mixed
     */
    public function getIcon();

    /**
     * @param $title
     * @return NotificationInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param $content
     * @return NotificationInterface
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param NotificationSubscriberInterface $subscriber
     * @return NotificationInterface
     */
    public function addSubscriber(NotificationSubscriberInterface $subscriber);

    /**
     * @param NotificationSubscriberInterface $subscriber
     * @return NotificationInterface
     */
    public function removeSubscriber(NotificationSubscriberInterface $subscriber);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubscribers();

    /**
     * @param BusinessInterface $member
     * @return NotificationSubscriberInterface|null
     */
    public function getSubscriber(SubscriberInterface $member);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}