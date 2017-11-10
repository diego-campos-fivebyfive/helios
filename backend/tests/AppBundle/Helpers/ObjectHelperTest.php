<?php

namespace Tests\AppBundle\Helpers;

trait ObjectHelperTest
{
    /**
     * @return float
     */
    protected static function randomFloat()
    {
        $first = self::randomInt(1, 50);
        $second = self::randomInt(100, 300);

        return floatval($second / $first);
    }

    /**
     * @param int $min
     * @param int $max
     * @return int
     */
    protected static function randomInt($min = 1, $max = 100)
    {
        return rand($min, $max);
    }

    /**
     * @param $size
     * @return string
     */
    protected static function randomString($size)
    {
        return substr(md5(time()), 0, $size);
    }

    /**
     * @param $object
     * @param array $data
     */
    protected static function fluentSetters($object, array $data)
    {
        foreach($data as $property => $value) {
            $setter = 'set' . ucfirst($property);
            $getter = 'get' . ucfirst($property);

            $object->$setter($value);
        }
    }

    /**
     * @param $object
     * @param array $data
     */
    protected function fluentSettersTest($object, array $data)
    {
        foreach($data as $property => $value) {

            $setter = 'set' . ucfirst($property);
            $getter = 'get' . ucfirst($property);
            $is = 'is' . ucfirst($property);

            $getter = method_exists($object, $getter) ? $getter : $is;

            $this->assertEquals($value, $object->$setter($value)->$getter());
        }
    }
}
