<?php

namespace AppBundle\Service\Support;

use Symfony\Component\HttpFoundation\ParameterBag;

class Widget extends ParameterBag implements WidgetInterface
{
    /**
     * @inheritDoc
     */
    public function toObject()
    {
        return json_decode($this->toJson());
    }

    /**
     * @inheritDoc
     */
    public function toJson()
    {
        return json_encode($this->all());
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    function __call($name, $arguments)
    {
        return $this->__get($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        return $this->toObject()->$name;
    }
}