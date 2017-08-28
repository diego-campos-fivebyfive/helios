<?php

namespace AppBundle\Service\Support\Project;

interface AreaDebuggerInterface
{
    const INCREASE_TEMP_MAX = 10;
    const DECREASE_TEMP_MIN = 5;

    /**
     * @param array $metadata
     * @return AreaDebuggerInterface
     */
    public function setMetadata(array $metadata);

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * @return array
     */
    public function getMetadataSchema();

    /**
     * @return true
     */
    public function isValid();

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return array
     */
    public function getResult();

    /**
     * @return object
     */
    public function getArea();
    
    /**
     * @return mixed
     */
    public function debug();
}