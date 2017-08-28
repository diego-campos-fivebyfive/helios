<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Parameter
 *
 * @ORM\Table(name="app_parameter")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Parameter extends ParameterBag
{
    use TokenizerTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var json
     *
     * @ORM\Column(name="parameters", type="json")
     */
    protected $parameters;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @inheritDoc
     */
    public function __construct(array $parameters = [], $id = null)
    {
        $this->id = $id;
        parent::__construct($parameters);
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

    public function getParameters()
    {
        return $this->all();
    }

    public function setParameters(array $parameters = [])
    {
        return $this->replace($parameters);
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }
}

