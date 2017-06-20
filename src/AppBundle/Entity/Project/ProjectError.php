<?php

namespace AppBundle\Entity\Project;

class ProjectError implements ProjectErrorInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var null
     */
    private $origin;

    /**
     * @inheritDoc
     */
    public function __construct($message, $origin = null)
    {
        $this->message = $message;
        $this->origin = $origin;
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
    public function getOrigin()
    {
        return $this->origin;
    }
}