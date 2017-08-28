<?php

namespace AppBundle\Model;


use AppBundle\Entity\Component\KitInterface;

class KitPricing
{
    const TARGET_EQUIPMENTS = 'equipments';
    const TARGET_SERVICES = 'services';
    const TARGET_GENERAL = 'general';

    public $id;

    public $name;

    public $target;

    public $percent;
    
    public $kit;

    function __construct(array $data = [])
    {
        if(empty($data) || !array_key_exists('id', $data) || !$data['id']) {
            $this->id = uniqid(time());
        }

        $this->fromArray($data);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromArray(array $data)
    {
        foreach($data as $property => $value){
            $this->$property = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $this->percent = str_replace(',', '.', $this->percent);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'target' => $this->target,
            'percent' =>  (float) $this->percent
        ];
    }

    /**
     * @return string json
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        if($this->kit instanceof KitInterface){

            $percent = $this->percent / 100;

            if($this->is(self::TARGET_EQUIPMENTS))
                return $this->kit->getPriceSaleEquipments() * $percent;

            if($this->is(self::TARGET_SERVICES))
                return $this->kit->getPriceSaleServices() * $percent;

            return $this->kit->getPriceSale() * $percent;
        }

        return 0;
    }

    /**
     * @param $target
     * @return bool
     */
    public function is($target)
    {
        return $target == $this->target;
    }

    /**
     * @return array
     */
    public static function getTargets()
    {
        return [
            'general' => 'general',
            'equipments' => 'equipments',
            'services' => 'services'
        ];
    }
}