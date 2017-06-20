<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Team
 *
 * @ORM\Table(name="app_team")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Team implements TeamInterface
{
    use TokenizerTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="enabled", type="integer")
     */
    private $enabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Customer", mappedBy="team")
     */
    private $members;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account", referencedColumnName="id")
     * })
     */
    private $account;

    public function __construct()
    {
        $this->members = new ArrayCollection();
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
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @inheritDoc
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function setAccount(BusinessInterface $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMember(BusinessInterface $member)
    {
        if(!$this->members->contains($member)){
            $this->members->add($member);
            $member->setTeam($this);
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
     * @inheritDoc
     */
    public function getLeader()
    {
        return $this->members->filter(function(BusinessInterface $member){
            return $member->isLeader();
        })->first();
    }
}

