<?php

namespace Tests\AppBundle\Helpers;

trait ObjectHelperTest
{
    /**
     * @param $object
     * @param array $data
     */
    protected function fluentSettersTest($object, array $data)
    {
        foreach($data as $property => $value) {

            $setter = 'set' . ucfirst($property);
            $getter = 'get' . ucfirst($property);

            $this->assertEquals($value, $object->$setter($value)->$getter());
        }
    }
}