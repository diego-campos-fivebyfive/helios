<?php

namespace AppBundle\Service;

use Kolina\CustomerBundle\Entity\CustomerManagerInterface;

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
     * @var CustomerManagerInterface
     */
    private $customerManager;

    function __construct(CustomerManagerInterface $customerManager)
    {
        $this->customerManager = $customerManager;
    }

    
}