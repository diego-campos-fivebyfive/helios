<?php

namespace AppBundle\Entity;

use AppBundle\Entity\BusinessInterface as SubscriberInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="app_notification")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Notification implements NotificationInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=25)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=25)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\NotificationSubscriber", mappedBy="notification", cascade={"persist","remove"})
     */
    private $subscribers;

    /**
     * Notification constructor.
     */
    function __construct()
    {
        $this->subscribers = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function addSubscriber(NotificationSubscriberInterface $subscriber)
    {
        if(!$this->subscribers->contains($subscriber)){
            $this->subscribers->add($subscriber);
            $subscriber->setNotification($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeSubscriber(NotificationSubscriberInterface $subscriber)
    {
        if($this->subscribers->contains($subscriber)){
            $this->subscribers->removeElement($subscriber);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @inheritDoc
     */
    public function getSubscriber(SubscriberInterface $member)
    {
        foreach($this->subscribers as $subscriber){
            if($subscriber->getSubscriber()->getId() === $member->getId()){
                return $subscriber;
            }
        }

        return null;
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

