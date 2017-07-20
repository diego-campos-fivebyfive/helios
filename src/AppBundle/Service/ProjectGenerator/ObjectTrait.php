<?php

namespace AppBundle\Service\ProjectGenerator;

trait ObjectTrait
{
    /**
     * @return array
     */
    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}