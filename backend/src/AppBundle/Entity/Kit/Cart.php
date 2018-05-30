<?php

namespace AppBundle\Entity\Kit;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kit
 * @ORM\Entity
 * @ORM\Table(name="app_kit")
 */
class Kit
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
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="power", type="float")
     */
    private $power;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer")
     */
    private $stock;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var boolean
     *
     * @ORM\Column(name="available", type="boolean")
     */
    private $available;

    /**
     * @var array
     *
     * @ORM\Column(name="components", type="json")
     */
    private $components;

    /**
     * Kit constructor.
     */
    public function __construct()
    {
        $this->components = [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Kit
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return Kit
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Kit
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @param float $power
     * @return Kit
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Kit
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     * @return Kit
     */
    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Kit
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return Kit
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * @param bool $available
     * @return Kit
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param array $components
     * @return Kit
     */
    public function setComponents(array $components)
    {
        $this->components = $components;

        return $this;
    }


    /**
     * @param array $newComponent
     * @return bool
     */
    public function addComponent($id, array $component)
    {
        if (!isset($this->components[$id])) {
            $this->components[$id] = $component;
        }
    }

    /**
     * @param $componentCode
     */
    public function removeComponent($componentId)
    {
        if (isset($this->components[$componentId])) {
            unset($this->components[$componentId]);
        }
    }
}
