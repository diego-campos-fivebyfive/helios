<?php

namespace AppBundle\Entity\Project;

interface ProjectErrorInterface
{
    /**
     * ProjectErrorInterface constructor.
     * @param $message
     * @param null $origin
     */
    function __construct($message, $origin = null);

    /**
     * @param $message
     * @return ProjectErrorInterface
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function getOrigin();
}