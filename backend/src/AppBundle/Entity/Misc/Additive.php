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
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;


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
}

