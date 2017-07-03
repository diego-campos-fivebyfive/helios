<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Webhook
 *
 * @ORM\Table(name="app_webhook")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Webhook implements WebhookInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="subscription_id", type="integer", nullable=true)
     */
    private $subscriptionId;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=25)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=25)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @inheritDoc
     */
    public function setSubscriptionId($id)
    {
        $this->subscriptionId = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @inheritDoc
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->context;
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
    public function prePersist()
    {
        $this->generateToken();

        $token = $this->getToken();

        $this->regenerateToken();

        $this->token = $token .'-'.$this->getToken();
    }
}

