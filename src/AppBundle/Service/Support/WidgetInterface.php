<?php

namespace AppBundle\Service\Support;

interface WidgetInterface
{
    /**
     * @return object
     */
    public function toObject();

    /**
     * @return string json
     */
    public function toJson();
}