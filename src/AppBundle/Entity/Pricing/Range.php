<?php

namespace AppBundle\Entity\Pricing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Range
 * @ORM\Entity
 * @ORM\Table(name="app_pricing_range")
 */
class Range implements RangeInterface
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
     * @var LevelInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pricing\Level", inversedBy="ranges")
     */
    private $level;

    /**
     * @ORM
     */

    /**
     * @var float
     *
     * @ORM\Column(name="initialPower", type="float")
     */
    private $initialPower;

    /**
     * @var float
     *
     * @ORM\Column(name="finalPower", type="float")
     */
    private $finalPower;

    /**
     * @var float
     *
     * @ORM\Column(name="markup", type="float")
     */
    private $markup;


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
     * Set initialPower
     *
     * @param float $initialPower
     *
     * @return Range
     */
    public function setInitialPower($initialPower)
    {
        $this->initialPower = $initialPower;

        return $this;
    }

    /**
     * Get initialPower
     *
     * @return float
     */
    public function getInitialPower()
    {
        return $this->initialPower;
    }

    /**
     * Set finalPower
     *
     * @param float $finalPower
     *
     * @return Range
     */
    public function setFinalPower($finalPower)
    {
        $this->finalPower = $finalPower;

        return $this;
    }

    /**
     * Get finalPower
     *
     * @return float
     */
    public function getFinalPower()
    {
        return $this->finalPower;
    }

    /**
     * Set markup
     *
     * @param float $markup
     *
     * @return Range
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;

        return $this;
    }

    /**
     * Get markup
     *
     * @return float
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * @inheritDoc
     */
    public function setLevel($level)
    {
        $this->level = $level;

        $level->addRange($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLevel()
    {
        return $this->level;
    }


}

