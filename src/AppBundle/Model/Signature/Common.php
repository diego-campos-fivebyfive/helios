<?php

namespace AppBundle\Model\Signature;

trait Common
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $code;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var object
     */
    protected $entity;

    /**
     * @param array $data
     * @return Common|object
     */
    public static function create(array $data = [])
    {
        $object = new self();
        foreach($data as $property => $value){
            $object->$property = $value;
        }

        return $object;
    }

    /**
     * @param $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        if(!is_object($entity)){
            throw new \InvalidArgumentException('Invalid object');
        }

        $properties = get_object_vars($this);
        foreach(get_object_vars($entity) as $property => $value){
            if(array_key_exists($property, $properties)){
                $target = $properties[$property];
                if(is_object($target) && method_exists($target, 'setEntity')){
                    if(is_object($value)) {
                        $target->setEntity($value);
                    }
                }else {
                    $this->$property = $value;
                }
            }
        }

        $this->entity = $entity;

        return $this;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        foreach (get_object_vars($this) as $property => $value){
            if(is_object($value) && method_exists($value, 'toArray')){
                $data[$property] = $value->toArray();
            }else{
                $data[$property] = $value;
            }
        }

        return $data;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors = [])
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return int|bool
     */
    public function hasErrors(){
        return count($this->errors);
    }

    /**
     * @return null|string
     */
    public function getError()
    {
        if($this->hasErrors()){
            $errors = array_values($this->errors);
            return $errors[0];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->entity && !$this->hasErrors();
    }

    /**
     * @inheritDoc
     */
    function ensure($target)
    {
        $properties = get_object_vars($this);
        foreach ($properties as $property => $value){
            if($property != $target){
                unset($this->$property);
            }
        }
    }
}