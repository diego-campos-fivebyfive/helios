<?php

namespace AppBundle\Entity\Kit;

use AppBundle\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 * @ORM\Entity
 * @ORM\Table(name="app_cart")
 */
class Cart
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Customer
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $account;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Customer
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Customer $account
     * @return $this
     */
    public function setAccount(Customer $account)
    {
        if ($account->isAccount()) {
            $this->account = $account;
        }

        return $this;
    }
}
