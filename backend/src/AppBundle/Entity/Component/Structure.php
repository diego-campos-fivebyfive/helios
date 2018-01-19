<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Structure
 *
 * @ORM\Table(name="app_component_structure")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Structure implements StructureInterface, ComponentInterface
{
    use ComponentTrait;

     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $code;

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
     * @ORM\Column(name="description", type="string")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"api"})
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="size", type="float", nullable=true)
     */
    private $size;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

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
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
    public function setType($type)
    {
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
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function getTypes()
    {
        return [
            self::TYPE_PROFILE,
            self::TYPE_JUNCTION,
            self::TYPE_TERMINAL,
            self::TYPE_FIXER,
            self::TYPE_BASE
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSubtypes()
    {
        return [
            self::ST_ROMAN,
            self::ST_INDUSTRIAL,
            self::ST_FINAL,
            self::ST_MIDDLE,
            self::ST_SCREW,
            self::ST_NUT,
            self::ST_HOOK,
            self::ST_SCREW_STR,
            self::ST_TRIANGLE_V,
            self::ST_TRIANGLE_H,
            self::ST_SCREW_AUTO,
            self::ST_TAPE,
            self::ST_HALF_METER,
            self::ST_SPEEDCLIP
        ];
    }
}
