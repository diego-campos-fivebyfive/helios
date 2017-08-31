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

/**
 * MetadataTrait
 * @author Fabio Jose <fabiojd47@gmail.com>
 */
trait MetadataTrait
{
    /**
     * @var array
     *
     * @ORM\Column(name="metadata", type="json", nullable=true)
     */
    protected $metadata = [];


    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null, $default = null)
    {
        if ($key) {
            return $this->hasMetadata($key) ? $this->metadata[$key] : $default;
        }

        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    public function hasMetadata($key = null)
    {
        if ($this->metadata) {
            if (!$key) {
                return !empty($this->metadata);
            }
            return array_key_exists($key, $this->metadata);
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function addMetadata($key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }
}