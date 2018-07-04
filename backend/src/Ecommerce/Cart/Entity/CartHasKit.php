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

use Doctrine\ORM\Mapping as ORM;
use Ecommerce\Kit\Entity\Kit;

/**
 * CartHasKit
 * @ORM\Entity
 * @ORM\Table(name="app_cart_has_kit")
 */
class CartHasKit
{
    /**
     * @var Cart
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Cart")
     */
    private $cart;

    /**
     * @var Kit
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Ecommerce\Kit\Entity\Kit")
     */
    private $kit;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param Cart $cart
     * @return CartHasKit
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return Kit
     */
    public function getKit()
    {
        return $this->kit;
    }

    /**
     * @param Kit $kit
     * @return CartHasKit
     */
    public function setKit(Kit $kit)
    {
        if ($kit->isAvailable()) {
            $this->kit = $kit;

            if ($this->quantity !== null && $this->quantity > $this->kit->getStock()) {
                $this->quantity = null;
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return CartHasKit
     */
    public function setQuantity(int $quantity)
    {
        if (($this->kit instanceof Kit && $this->kit->getStock() >= $quantity && $quantity > 0)
            || $this->kit === null) {
            $this->quantity = $quantity;
        }

        return $this;
    }
}
