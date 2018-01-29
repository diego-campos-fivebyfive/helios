<?php

namespace AppBundle\Entity\Misc;

use Doctrine\ORM\Mapping as ORM;

/**
 * Additive
 *
 * @ORM\Table(name="app_additive")
 * @ORM\Entity
 */
class Additive implements AdditiveInterface
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
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

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
     * @var int
     *
     * @ORM\Column(name="target", type="smallint")
     */
    private $target;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var array
     *
     * @ORM\Column(name="required_levels", type="json", nullable=true)
     */
    private $requiredLevels;

    /**
     * @var array
     *
     * @ORM\Column(name="available_levels", type="json", nullable=true)
     */
    private $availableLevels;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @var float
     *
     * @ORM\Column(name="min_power", type="float", nullable=true)
     */
    private $minPower;

    /**
     * @var float
     *
     * @ORM\Column(name="max_power", type="float", nullable=true)
     */
    private $maxPower;

    /**
     * @var float
     *
     * @ORM\Column(name="min_price", type="float", nullable=true)
     */
    private $minPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="max_price", type="float", nullable=true)
     */
    private $maxPrice;

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
     * Set type
     *
     * @param integer $type
     *
     * @return Additive
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Additive
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
     * Set description
     *
     * @param string $description
     *
     * @return Additive
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
     * Set target
     *
     * @param integer $target
     *
     * @return Additive
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return int
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Additive
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setRequiredLevels($requiredLevels)
    {
        $this->requiredLevels = $requiredLevels;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequiredLevels()
    {
        return $this->requiredLevels;
    }

    /**
     * @inheritDoc
     */
    public function setAvailableLevels($availableLevels)
    {
        $this->availableLevels = $availableLevels;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableLevels()
    {
        return $this->availableLevels;
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
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param $level
     * @return bool
     */
    public function isRequiredByLevel($level)
    {
        return in_array($level, $this->requiredLevels);
    }

    /**
     * @param $level
     * @return bool
     */
    public function isAvailableByLevel($level)
    {
        return in_array($level, $this->availableLevels);
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_INSURANCE => 'insurance'
        ];
    }

    /**
     * @return array
     */
    public static function getTargets()
    {
        return [
            self::TARGET_FIXED => 'fixed value',
            self::TARGET_PERCENT => 'system percentage'
        ];
    }

    /**
     * @inheritDoc
     */
    public function setMinPower($power)
    {
        $this->minPower = $power;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMinPower()
    {
        return $this->minPower;
    }

    /**
     * @inheritDoc
     */
    public function setMaxPower($power)
    {
        $this->maxPower = $power;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxPower()
    {
        return $this->maxPower;
    }

    /**
     * @inheritDoc
     */
    public function setMinPrice($price)
    {
        $this->minPrice = $price;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMinPrice()
    {
        return $this->minPrice;
    }

    /**
     * @inheritDoc
     */
    public function setMaxPrice($price)
    {
        $this->maxPrice = $price;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }
}

