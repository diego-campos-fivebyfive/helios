<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Task
 *
 * @ORM\Table(name="app_task")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Task implements TaskInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_at", type="datetime")
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime")
     */
    private $endAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="authoredTasks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * })
     */
    private $author;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="contactTasks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $contact;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Customer", inversedBy="assignedTasks")
     * @ORM\JoinTable(name="app_task_member",
     *   joinColumns={
     *     @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     *   }
     * )
     */
    private $members;

    function __construct()
    {
        $this->type = self::TYPE_UNDEFINED;
        $this->status = self::STATUS_ENABLED;
        $this->members = new ArrayCollection();

        $this->startAt = new \DateTime();
        $this->startAt->setTime(9,0,0);

        $this->endAt = new \DateTime();
        $this->endAt->setTime(9,30);
    }

    /**
     * @inheritDoc
     */
    public static function getFilterData()
    {
        return ['type', 'status'];
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
    public function isPending()
    {
        return self::STATUS_ENABLED == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isDone()
    {
        return self::STATUS_DONE == $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setAuthor(BusinessInterface $author)
    {
        if(!$author->isMember())
            $this->exceptionInvalidContext();

        $author->addAuthoredTask($this);
        $this->author = $author;

        $this->addMember($author);

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
    public function setContact(BusinessInterface $contact)
    {
        if(!$contact->isContact())
            $this->exceptionInvalidContext();

        $contact->addContactTask($this);
        $this->contact = $contact;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @inheritDoc
     */
    public function addMember(BusinessInterface $member)
    {
        if(!$member->isMember())
            $this->exceptionInvalidContext();

        if(!$this->members->contains($member)){
            $this->members->add($member);
            $member->addAssignedTask($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeMember(BusinessInterface $member)
    {
        if($this->members->contains($member)){
            $this->members->removeElement($member);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setStartAt(\DateTime $startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @inheritDoc
     */
    public function setEndAt(\DateTime $endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEndAt()
    {
        return $this->endAt;
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
     * Set status
     *
     * @param integer $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getStatusTag()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status];
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key)
    {
        return [
            'token' => $this->token,
            'title' => $this->getDescription(),
            'start' => $this->startAt->format('Y-m-d H:i'),
            'end' => $this->endAt->format('Y-m-d H:i'),
            'icon' => sprintf('fa %s', $this->getIconType())
        ];
    }

    /**
     * @inheritDoc
     */
    public function getCreateAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function getIconType()
    {
        $icons = self::getTypeIcons(false);

        if(array_key_exists($this->type, $icons)){
            return $icons[$this->type];
        }

        return $icons['undefined'];
    }

    /**
     * @inheritDoc
     */
    public function getTagType()
    {
        $types = self::getTypes();
        return $types[$this->type];
    }

    /**
     * @inheritDoc
     */
    public static function getTypes()
    {
        return [
            self::TYPE_UNDEFINED => 'general',
            self::TYPE_MAIL => 'mail',
            self::TYPE_CALL => 'call',
            self::TYPE_PROPOSAL => 'proposal',
            self::TYPE_REUNION => 'reunion',
            self::TYPE_VISITATION => 'visitation'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ENABLED => 'enabled',
            //self::STATUS_DISABLED => 'disabled',
            //self::STATUS_DELAY => 'delay',
            self::STATUS_DONE => 'done'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getTypeIcons($index = true)
    {
        $types = self::getTypes();

        return [
            !$index ? self::TYPE_UNDEFINED  : $types[self::TYPE_UNDEFINED]  => 'fa-info',
            !$index ? self::TYPE_MAIL       : $types[self::TYPE_MAIL]       => 'fa-envelope',
            !$index ? self::TYPE_CALL       : $types[self::TYPE_CALL]       => 'fa-phone',
            !$index ? self::TYPE_PROPOSAL   : $types[self::TYPE_PROPOSAL]   => 'fa-file-text',
            !$index ? self::TYPE_REUNION    : $types[self::TYPE_REUNION]    => 'fa-users',
            !$index ? self::TYPE_VISITATION : $types[self::TYPE_VISITATION] => 'fa-map-marker'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFilterChoices($option = null)
    {
        return ['all' => 'all', 'week' => 'week', 'today' => 'today'];
    }

    /**
     * @inheritDoc
     */
    public function prePersist()
    {
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        $this->generateToken();
    }

    private function exceptionInvalidContext()
    {
        throw new \InvalidArgumentException(self::ERROR_INVALID_CONTEXT);
    }
}

