<?php

namespace AppBundle\Entity\Order;

use AppBundle\Entity\MemberInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Message
 *
 * @ORM\Table(name="app_order_message")
 * @ORM\Entity
 */
class Message implements MessageInterface
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $restricted;

    /**
     * @var array
     *
     * @ORM\Column(name="to_users", type="simple_array", nullable=true)
     */
    private $to;

    /**
     * @var array
     *
     * @ORM\Column(name="read_users", type="simple_array", nullable=true)
     */
    private $read;

    /**
     * @var OrderInterface
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="messages")
     */
    protected $order;

    /**
     * @var MemberInterface|null
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    protected $author;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->to = [];
        $this->read = [];
    }

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
     * Set content
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function setOrder($order)
    {
        $this->order = $order;

        $order->addMessage($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @inheritDoc
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @inheritDoc
     */
    public function setRestricted($restricted)
    {
        $this->restricted = $restricted;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRestricted()
    {
        return $this->restricted;
    }

    /**
     * @inheritDoc
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @inheritDoc
     */
    public function setTo(array $to)
    {
        $data = array_map('strval', $to);

        $this->to = $data;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @inheritDoc
     */
    public function setRead(array $read)
    {
        $data = array_map('strval', $read);

        $this->read = $data;

        return $this;
    }
}

