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
            if('id' != $property) {
                $accessor->setValue($element, $property, $value);
            }
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
        $element = self::getElement($source, $data);
        $selfData = self::getSelfData($source, $data);

        if($selfData) {
            self::update($element, self::extract($selfData));
        }

        return $element;
    }

    /**
     * @param $source
     * @param null $data
     * @return Element|null
     */
    private static function getElement($source, $data = null)
    {
        if ($data instanceof Element) return $data;

        if ($source instanceof Element) return $source;

        return new Element();
    }

    /**
     * @param $source
     * @param $data
     * @return mixed
     */
    private static function getSelfData($source, $data)
    {
        if ($source instanceof Element) return $data;

        if($data instanceof Element) return $source;

        return null;
    }
}