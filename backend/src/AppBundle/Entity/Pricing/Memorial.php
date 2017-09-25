<?php

namespace AppBundle\Entity\Pricing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Memorial
 * @ORM\Entity
 * @ORM\Table(name="app_pricing_memorial")
 */
class Memorial implements MemorialInterface
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Range", mappedBy="memorial", cascade={"persist", "remove"})
     */
    private $ranges;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startAt;

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
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    protected $levels;

    /**
     * Memorial constructor.
     */
    function __construct()
    {
        $this->ranges = new ArrayCollection();
        $this->status = self::STATUS_PENDING;
        $this->levels = self::getDefaultLevels(true);
    }

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status
        ];
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
    public function setStartAt($startAt)
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
    public function setExpiredAt(\DateTime $expiredAt)
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * @inheritDoc
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        switch ($status){
            case self::STATUS_PENDING:

                $this->publishedAt = null;
                $this->expiredAt = null;

                break;

            case self::STATUS_PUBLISHED:
                $this->expiredAt = null;
                $this->publishedAt = new \DateTime();
                break;

            case self::STATUS_EXPIRED:
                $this->expiredAt = new \DateTime();
                break;
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus($label = false)
    {
        return  !$label ? $this->status : self::getStatuses()[$this->status] ;
    }

    /**
     * @inheritDoc
     */
    public function isExpired()
    {
        return self::STATUS_EXPIRED === $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isPending()
    {
        return self::STATUS_PENDING === $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isPublished()
    {
        return self::STATUS_PUBLISHED === $this->status;
    }

    /**
     * @inheritDoc
     */
    public function addRange(RangeInterface $range)
    {
        if(!$this->ranges->contains($range)) {
            $this->ranges->add($range);

            if(!$range->getMemorial()){
                $range->setMemorial($this);
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * @inheritDoc
     */
    public function setLevels(array $levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * @inheritDoc
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_EXPIRED => 'Expired'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultLevels($keys = false)
    {
        $levels = [
            self::LEVEL_BLACK => 'Black',
            self::LEVEL_GOLD => 'Gold',
            self::LEVEL_PLATINUM => 'Platinum',
            self::LEVEL_PREMIUM => 'Premium',
            self::LEVEL_PARTNER => 'Partner',
            self::LEVEL_PROMOTIONAL => 'Promotional'
        ];

        return $keys ? array_keys($levels) : $levels ;
    }
}
