<?php

namespace AppBundle\Entity\Pricing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Range
 *
 * @ORM\Table(name="app_pricing_range")
 * @ORM\Entity
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
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Memorial", inversedBy="ranges")
     */
    private $memorial;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

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
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=255)
     */
    private $level;

    /**
     * @var float
     *
     * @ORM\Column(name="markup", type="float")
     */
    private $markup;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    public function __construct()
    {
        //$this->memorial = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     *
     * @return Range
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
     * Set level
     *
     * @param string $level
     *
     * @return Range
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
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
     * Set price
     *
     * @param string $price
     *
     * @return Range
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @inheritDoc
     */
    public function setMemorial($memorial)
    {
        $this->memorial = $memorial;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMemorial()
    {
        return $this->memorial;
    }
}

