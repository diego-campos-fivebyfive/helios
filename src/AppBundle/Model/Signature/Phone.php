<?php

namespace AppBundle\Model\Signature;

class Phone
{
    public $phone_type = 'mobile';

    public $number;

    public $extension;

    /**
     * Phone constructor.
     * @param null $number
     */
    function __construct($number= null)
    {
        if($number){
            $this->fromNumber($number);
        }
    }

    /**
     * @param $number
     */
    public function fromNumber($number){
        $number = '55'.preg_replace('/[^0-9]/', '', $number);
        $this->number = $number;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'phone_type' => $this->phone_type,
            'number' => $this->number,
            'extension' => $this->extension
        ];
    }
}