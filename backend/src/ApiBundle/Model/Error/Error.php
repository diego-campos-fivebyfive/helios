<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ApiBundle\Model\Error;

/**
 * This class is a default template for errors in api response.
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Error implements ErrorInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $info;

    function __construct($code = null, $type = null, $message = null, $info = null)
    {
        $this->code = $code;
        $this->type = $type;
        $this->message = $message;
        $this->info = $info;
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
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}