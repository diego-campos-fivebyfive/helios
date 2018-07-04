<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Cart\Entity;

use AppBundle\Entity\AccountInterface;
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
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $checkout;

    /**
     * Cart constructor.
     */
    public function __construct()
    {
        $this->checkout = [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AccountInterface
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param AccountInterface $account
     * @return $this
     */
    public function setAccount(AccountInterface $account)
    {
        if ($account->isAccount()) {
            $this->account = $account;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCheckout()
    {
        return $this->checkout;
    }

    /**
     * @param array $checkout
     * @return $this
     */
    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;

        return $this;
    }
}
