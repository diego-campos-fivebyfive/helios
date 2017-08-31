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
    private $isquik_id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Range", mappedBy="memorial", cascade={"persist"})
     */
    private $ranges;

    /**
     * @var float
     *
     * @ORM\Column(name="version", type="float", nullable=true)
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

    function __construct()
    {
        $this->ranges = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return (string) $this->version . ' - ' . $this->startAt->format('d/m/Y');
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
    public function setIsquikId($isquik_id)
    {
        $this->isquik_id = $isquik_id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsquikId()
    {
        return $this->isquik_id;
    }
    
    /**
     * Set version
     *
     * @param float $version
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
     * @return float
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
