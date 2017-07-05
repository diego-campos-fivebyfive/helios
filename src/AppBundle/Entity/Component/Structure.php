<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\AccountInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use AppBundle\Model\Snapshot;

/**
 * Structure
 *
 * @ORM\Table(name="app_component_structure")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Structure implements StructureInterface
{

    use TokenizerTrait;
    use ComponentTrait;
    use Snapshot;

     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", nullable=true)
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
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="size", type="float", nullable=true)
     */
    private $size;

    /**
     * @var MakerInterface
     *
     * @ORM\ManyToOne(targetEntity="Maker", inversedBy="structures")
     * @ORM\JoinColumn(name="maker")
     */
    protected $maker;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
        $this->generateToken();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();

        return $this;
    }
}
