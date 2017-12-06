<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Misc;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait AdditiveRelationTrait
 * @package AppBundle\Entity\Misc
 *
 * @ORM\Entity
 */
trait AdditiveRelationTrait
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float")
     */
    private $total;

    /**
     * @var AdditiveInterface
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Misc\Additive")
     * @ORM\JoinColumn(name="additive_id", referencedColumnName="id")
     */
    private $additive;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->total = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function setTotal($total)
    {
        $this->total = (float) $total;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function setAdditive(AdditiveInterface $additive)
    {
        $this->additive = $additive;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdditive()
    {
        return $this->additive;
    }
}
