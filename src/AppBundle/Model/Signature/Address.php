<?php

namespace AppBundle\Model\Signature;

class Address
{
    use Common;

    /**
     * @var string
     */
    public $street;

    /**
     * @var string
     */
    public $number;

    /**
     * @var string
     */
    public $additional_details;

    /**
     * @var string
     */
    public $zipcode;

    /**
     * @var string
     */
    public $neighborhood;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $country;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->country = 'BR';
    }
}