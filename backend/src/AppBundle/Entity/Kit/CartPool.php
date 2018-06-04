<?php

namespace AppBundle\Entity\Kit;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart Pool
 * @ORM\Entity
 * @ORM\Table(name="app_cart_pool")
 */
class CartPool
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $code;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $metadata;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $callbacks;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->metadata = [];
        $this->callbacks = [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setCode($code)
    {
        $this->code = $code;

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
     * @inheritdoc
     */
    public function setMetadata(array $metadata = [])
    {
        $this->metadata = $metadata;

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
     * @inheritdoc
     */
    public function setCallbacks(array $callbacks = [])
    {
        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }
}
