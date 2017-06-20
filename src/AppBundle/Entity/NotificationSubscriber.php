<?php

namespace AppBundle\Entity;

use AppBundle\Entity\BusinessInterface as SubscriberInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationSubscriber
 *
 * @ORM\Table(name="app_notification_subscriber")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class NotificationSubscriber implements NotificationSubscriberInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="viewed_at", type="datetime", nullable=true)
     */
    private $viewedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Notification
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Notification", inversedBy="subscribers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="notification_id", referencedColumnName="id")
     * })
     */
    private $notification;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")
     * })
     */
    private $subscriber;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setNotification(NotificationInterface $notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @inheritDoc
     */
    public function setSubscriber(SubscriberInterface $subscriber)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @inheritDoc
     */
    public function setViewedAt(\DateTime $viewedAt)
    {
        $this->viewedAt = $viewedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * @inheritDoc
     */
    public function isViewed()
    {
        return $this->viewedAt instanceof \DateTime;
    }

    /**
     * @inheritDoc
     */
    public function getAddedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     *
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->generateToken();
    }
}

