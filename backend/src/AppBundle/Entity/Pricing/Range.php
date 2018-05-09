<?php

namespace AppBundle\Entity\Pricing;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Range
 *
 * @ORM\Table(name="app_pricing_range")
 * @ORM\Entity
 */
class Range implements RangeInterface
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
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price", type="float")
     */
    private $costPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="tax", type="float")
     */
    private $tax;

    /**
     * @var float
     *
     * @ORM\Column(name="markup", type="float")
     */
    private $markup;

    public function __construct()
    {
        $this->tax = self::DEFAULT_TAX;
        $this->markup = 0;
        $this->costPrice = 0;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = get_object_vars($this);

        $data['memorial'] = $this->getMemorial()->getId();

        return $data;
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
     * @inheritDoc
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice =  (float) $costPrice;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCostPrice()
    {
        return (float) $this->costPrice;
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
        $this->price = (float) $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return (float) $this->price;
    }

    /**
     * @inheritDoc
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @inheritDoc
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMarkup($percent = false)
    {
        return !$percent ? $this->markup : $this->markup * 100;
    }

    /**
     * @inheritDoc
     */
    public function setMemorial(MemorialInterface $memorial)
    {
        $this->memorial = $memorial;

        $memorial->addRange($this);

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
    public function hasConfig($code, $level, $initialPower, $finalPower)
    {
        foreach (get_object_vars($this) as $property => $value){
            if(isset($$property)){
                if($value != $$property){
                    return false; break;
                }
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function updatePrice()
    {
        if(0 == $this->tax) $this->tax = self::DEFAULT_TAX;

        $this->price = round($this->costPrice * (1 + $this->markup) / (1 - $this->tax), 2);

        return $this;
    }
}

