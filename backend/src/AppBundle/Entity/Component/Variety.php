<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Variety
 *
 * @ORM\Table(name="app_component_variety")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Variety implements VarietyInterface, ComponentInterface
{
    use ComponentTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="subtype", type="string", nullable=true)
     */
    private $subtype;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $power;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean", nullable=true)
     */
    private $required;

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return (string) $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        if(!in_array($type, self::getTypes())){
            throw new \InvalidArgumentException(sprintf('Invalid type [%s]', $type));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setSubType($subtype)
    {
        $this->subtype = $subtype;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubType()
    {
        return $this->subtype;
    }

    /**
     * @inheritDoc
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @inheritDoc
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequired()
    {
        return $this->required;
    }


    /**
     * @inheritDoc
     */
    public static function getTypes()
    {
        return [
            self::TYPE_STRING_BOX,
            self::TYPE_ABB_EXTRA,
            self::TYPE_CABLE,
            self::TYPE_CONNECTOR,
            self::TYPE_TRANSFORMER,
            self::TYPE_MONITOR,
            self::TYPE_FUSE
        ];
    }
}
