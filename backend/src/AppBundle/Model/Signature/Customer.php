<?php

namespace AppBundle\Model\Signature;

class Customer
{
    use Common;

    /**
     * @var string
     */
    public $name;

    /**
     * @var
     */
    public $email;

    /**
     * @var string
     */
    public $registry_code;

    /**
     * @var Address
     */
    public $address;

    /**
     * @var
     */
    public $phones;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->address = Address::create();
        $this->phones = [];
    }

    public function addPhone($number){
        $phone = new Phone($number);
        $this->phones[] = $phone->toArray();
    }
}