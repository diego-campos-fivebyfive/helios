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
    public static function getTypes()
    {
        return [
            self::TYPE_PROFILE => 'Perfil',
            self::TYPE_JUNCTION => 'Junção',
            self::TYPE_TERMINAL => 'Terminal',
            self::TYPE_FIXER => 'Fixador',
            self::TYPE_BASE => 'Base'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getSubtypes()
    {
        return [
            self::ST_ROMAN => 'Roman',
            self::ST_INDUSTRIAL => 'Industrial',
            self::ST_FINAL => 'Final',
            self::ST_MIDDLE => 'Intermediário',
            self::ST_SCREW => 'Parafuso',
            self::ST_NUT => 'Porca',
            self::ST_HOOK => 'Gancho',
            self::ST_SCREW_STR => 'Parafuso estrutural',
            self::ST_TRIANGLE_V => 'Triangulo vertical',
            self::ST_TRIANGLE_H => 'Triangulo horizontal',
            self::ST_SCREW_AUTO => 'Parafuso autoperfurante',
            self::ST_TAPE => 'Fita',
            self::ST_HALF_METER => 'Meio metro',
            self::ST_SPEEDCLIP => 'Speedclip'
        ];
    }
}
