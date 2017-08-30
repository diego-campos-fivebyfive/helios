<?php

namespace AppBundle\Service;

use AppBundle\Manager\CustomerManager;

/**
 * Manipulate, helper, filter or complex functions for
 * - Account
 * - Member
 * - Contact [Company and Person]
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class CustomerHelper
{
    /**
     * @var CustomerManager
     */
    private $customerManager;

    function __construct(CustomerManager $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    
}