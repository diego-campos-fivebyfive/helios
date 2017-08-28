<?php

namespace AppBundle\Model;

trait Snapshot
{
    /**
     * @param array $options
     * @return array
     */
    public function snapshot(array $options = [])
    {
        $ignored = [
            '__isInitialized__'
        ];

        $vars = get_object_vars($this);

        foreach($vars as $property => $value){
            if(is_object($value)){
                $vars[$property] = $this->snapshotObject($value);
            }
            if(in_array($property, $ignored)){
                unset($vars[$property]);
            }
        }

        return array_filter($vars);
    }

    /**
     * @param $object
     * @return null|string
     */
    private function snapshotObject($object)
    {
        if($object instanceof \DateTime){
            return $object->format('Y-m-d H:i:s');
        }
        if(method_exists($object, 'snapshot')){
            return $object->snapshot();
        }
        if(method_exists($object, '__toString')){
            return (string) $object;
        }

        return null;
    }
}