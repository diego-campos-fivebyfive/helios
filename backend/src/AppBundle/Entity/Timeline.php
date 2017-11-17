<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timeline
 *
 * @ORM\Table(name="app_timeline")
 * @ORM\Entity
 */
class Timeline implements TimelineInterface
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
     * @ORM\Column(name="target", type="string", length=255, nullable=true)
     */
    private $target;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @var json
     *
     * @ORM\Column(name="attributes", type="json", nullable=true)
     */
    private $attributes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * Timeline constructor.
     */
    public function __construct()
    {
        $this->attributes = [];
    }


    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set target
     * @param string $target
     * @return Timeline
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set message
     * @param string $message
     * @return Timeline
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAttribute($key)
    {
        if($this->hasAttribute($key)){
            unset($this->attributes[$key]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($key, $default = null)
    {
        if($this->hasAttribute($key)){
            return $this->attributes[$key];
        }
        return $default;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set createdAt
     * @param \DateTime $createdAt
     * @return Timeline
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

