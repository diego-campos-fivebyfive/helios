<?php

namespace AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ElementResolver
{
    /**
     * @param Element $element
     * @param array|object $data
     */
    public static function update(Element $element, $data)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($data as $property => $value) {
            $accessor->setValue($element, $property, $value);
        }
    }

    /**
     * @param array $data
     * @return Element
     */
    public static function create(array $data)
    {
        $element = new Element();

        self::update($element, $data);

        return $element;
    }

    /**
     * @param $source
     * @return array
     */
    public static function extract($source)
    {
        return ComponentExtractor::extract($source);
    }

    /**
     * @param $source
     * @param null $data
     * @return Element|null
     */
    public static function resolve($source, $data = null)
    {
        $selfData = null;
        $element = self::resolveElement($source, $data, $selfData);

        if($selfData) {
            self::update($element, self::extract($selfData));
        }

        return $element;
    }

    /**
     * @param $source
     * @param null $data
     * @param null $selfData
     * @return Element|null
     */
    private function resolveElement($source, $data = null, &$selfData = null)
    {
        if($source instanceof Element) {
            $selfData = $data;
            return $source;
        }else{
            $selfData = $source;
        }

        if($data instanceof Element) {
            $selfData = $source;
            return $data;
        }

        return new Element();
    }
}