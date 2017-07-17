<?php

namespace AppBundle\Entity\Component;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Maker
 *
 * @ORM\Table(name="app_component_maker")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Maker implements MakerInterface
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=20)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    function __construct()
    {
        $this->enabled = true;
    }

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return $this->name;
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
     * @inheritDoc
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Maker
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Maker
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @inheritDoc
     */
    public function isMakerInverter()
    {
        return $this->context === self::CONTEXT_INVERTER;
        //|| $this->context === self::CONTEXT_ALL;
    }

    /**
     * @inheritDoc
     */
    public function isMakerModule()
    {
        return $this->context === self::CONTEXT_MODULE;
        //|| $this->context === self::CONTEXT_ALL;
    }

    /**
     * @inheritDoc
     */
    public function isMakerStructure()
    {
        return $this->context === self::CONTEXT_STRUCTURE;
        //|| $this->context === self::CONTEXT_ALL;
    }

    /**
     * @inheritDoc
     * @deprecated context ALL is removed
     */
    public function isMakerAll()
    {
        throw new \BadMethodCallException('This method is disabled');
        //return $this->context == self::CONTEXT_ALL;
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public static function getContextList()
    {
        return [
            self::CONTEXT_MODULE => self::CONTEXT_MODULE,
            self::CONTEXT_INVERTER => self::CONTEXT_INVERTER
            //self::CONTEXT_ALL => self::CONTEXT_ALL
        ];
    }

    /**
     * @inheritDoc
     */
    public static function unsupportedMakerContextException()
    {
        throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_CONTEXT);
    }
}

