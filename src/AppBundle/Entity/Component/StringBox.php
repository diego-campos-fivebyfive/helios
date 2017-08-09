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

/**
 * StringBox
 *
 * @ORM\Table(name="app_component_string_box")
 * @ORM\Entity
 */
class StringBox implements StringBoxInterface
{
    use ComponentTrait;

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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="inputs", type="smallint", nullable=true)
     */
    private $inputs;

    /**
     * @var int
     *
     * @ORM\Column(name="outputs", type="smallint", nullable=true)
     */
    private $outputs;

    /**
     * @var int
     *
     * @ORM\Column(name="fuses", type="smallint", nullable=true)
     */
    private $fuses;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @inheritDoc
     */
    function __toString()
    {
        return $this->description;
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
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @inheritDoc
     */
    public function setOutputs($outputs)
    {
        $this->outputs = $outputs;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * @inheritDoc
     */
    public function setFuses($fuses)
    {
        $this->fuses = $fuses;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFuses()
    {
        return $this->fuses;
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
