<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Context
 *
 * @ORM\Table(name="app_postcode")
 * @ORM\Entity
 */
class Postcode
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @var array
     *
     * @ORM\Column(name="attributes", type="json")
     */
    protected $attributes;

    public function __construct()
    {
        $this->attributes = [
            'postcode' => '',
            'state' => '',
            'city' => '',
            'neighborhood' => '',
            'street' => ''
        ];
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = str_replace('-', '', $id);

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttribute($attribute)
    {
        return $this->attributes[$attribute];
    }
}

