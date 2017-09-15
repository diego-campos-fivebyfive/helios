<?php

namespace AppBundle\Entity\Pricing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Memorial
 * @ORM\Entity
 * @ORM\Table(name="app_pricing_memorial")
 */
class Memorial implements MemorialInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="isquik_id", type="integer", nullable=true)
     */
    private $isquikId;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Range", mappedBy="memorial", cascade={"persist", "remove"})
     */
    private $ranges;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", nullable=true)
     */
    private $version;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startAt", type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endAt", type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    function __construct()
    {
        $this->ranges = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return (string) sprintf('V%s - %s', $this->version, $this->startAt->format('Y-m-d'));
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'isquik_id' => $this->isquikId,
            'version' => $this->version,
            'start_at' => $this->startAt->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'description' => $this->description
        ];
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
     * @inheritDoc
     */
    public function setIsquikId($isquikId)
    {
        $this->isquikId = $isquikId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsquikId()
    {
        return $this->isquikId;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Memorial
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return Memorial
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return Memorial
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @inheritDoc
     */
    public function getTax()
    {
        return 0.0925;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Memorial
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
}
