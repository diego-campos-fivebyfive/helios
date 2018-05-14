<?php

namespace AppBundle\Entity\Precifier;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Memorial
 * @ORM\Entity
 * @ORM\Table(name="app_precifier_memorial")
 * @ORM\HasLifecycleCallbacks()
 */
class Memorial
{
    const STATUS_PENDING = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_EXPIRED = 2;

    const LEVEL_TITANIUM = 'titanium';
    const LEVEL_BLACK = 'black';
    const LEVEL_GOLD = 'gold';
    const LEVEL_PARTNER = 'partner';
    const LEVEL_PLATINUM = 'platinum';
    const LEVEL_PREMIUM = 'premium';
    const LEVEL_PROMOTIONAL = 'promotional';
    const LEVEL_FINAME = 'finame';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiredAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Range", mappedBy="memorial", cascade={"persist", "remove"})
     */
    private $ranges;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Memorial constructor.
     */
    public function __construct()
    {
        $this->ranges = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Memorial
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Memorial
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Memorial
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * @param \DateTime $expiredAt
     * @return Memorial
     */
    public function setExpiredAt($expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     * @return Memorial
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return ArrayCollection
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * @param Range $range
     * @return $this
     */
    public function addRange(Range $range)
    {
        if (!$this->ranges->contains($range)) {
            $this->ranges->add($range);

            if (!$range->getMemorial()) {
                $range->setMemorial($this);
            }
        }

        return $this;
    }

    /**
     * @param Range $range
     * @return $this
     */
    public function removeRange(Range $range)
    {
        if ($this->ranges->contains($range)) {
            $this->ranges->removeElement($range);
        }

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultLevels($keys = false)
    {
        $levels = [
            self::LEVEL_TITANIUM => 'Titanium',
            self::LEVEL_BLACK => 'Black',
            self::LEVEL_PLATINUM => 'Platinum',
            self::LEVEL_PREMIUM => 'Premium',
            self::LEVEL_PARTNER => 'Partner',
            self::LEVEL_PROMOTIONAL => 'Promotional',
            self::LEVEL_FINAME => 'Finame'
        ];

        return $keys ? array_keys($levels) : $levels ;
    }
}
