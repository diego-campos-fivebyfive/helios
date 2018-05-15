<?php

namespace AppBundle\Entity\Precifier;

use Doctrine\ORM\Mapping as ORM;

/**
 * Range
 * @ORM\Entity
 * @ORM\Table(name="app_precifier_range")
 */
class Range
{
    const DEFAULT_TAX = 0.0925;

    /**
     * @var array
     */
    public static $powerRanges = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Memorial
     *
     * @ORM\ManyToOne(targetEntity="Memorial", inversedBy="ranges")
     */
    private $memorial;

    /**
     * @var int
     *
     * @ORM\Column(name="component_id", type="integer")
     */
    private $componentId;

    /**
     * @var string
     *
     * @ORM\Column(name="family", type="string")
     */
    private $family;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="cost_price", type="float")
     */
    private $costPrice;

    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json")
     */
    private $metadata;

    /**
     * Range constructor.
     */
    public function __construct()
    {
        $this->costPrice = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Memorial
     */
    public function getMemorial()
    {
        return $this->memorial;
    }

    /**
     * @param Memorial $memorial
     * @return Range
     */
    public function setMemorial(Memorial $memorial)
    {
        $this->memorial = $memorial;

        $memorial->addRange($this);

        return $this;
    }

    /**
     * @return int
     */
    public function getComponentId()
    {
        return $this->componentId;
    }

    /**
     * @param int $componentId
     * @return Range
     */
    public function setComponentId($componentId)
    {
        $this->componentId = $componentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @param string $family
     * @return Range
     */
    public function setFamily($family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Range
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return float
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * @param float $costPrice
     * @return Range
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     * @return Range
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }
}