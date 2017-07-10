<?php

namespace AppBundle\Entity\Pricing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Level
 *
 * @ORM\Entity
 * @ORM\Table(name="app_pricing_level")
 */
class Level implements LevelInterface
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
     * @var MemorialInteface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pricing\Memorial", inversedBy="levels")
     */
    private $memorial;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pricing\Range", mappedBy="level", cascade={"persist"})
     */
    private $ranges;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    public function __construct()
    {
        $this->memorial = new ArrayCollection();
        $this->ranges = new ArrayCollection();
    }

    function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     *
     * @return Level
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @inheritDoc
     */
    public function addMemorial($memorial)
    {
        $this->memorial = $memorial;
    }

    /**
     * @inheritDoc
     */
    public function setMemorial($memorial)
    {
        $this->memorial = $memorial;

        $memorial->addLevel($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMemorial()
    {
        return $this->memorial;
    }

    /**
     * @inheritDoc
     */
    public function addRanges($range)
    {
        if(!$this->ranges->contains($range)) {
            $this->ranges->add($range);

            if(!$range->getLevel()){
                $range->setLevel($this);
            }
        }

        return $this;
    }


}

