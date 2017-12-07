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
     * @var int
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="target", type="smallint", nullable=true)
     */
    private $target;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float", nullable=true)
     */
    private $value;

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
        if ($this->target == AdditiveInterface::TARGET_FIXED)
            return round($this->value, 2);
        else
            return round(($this->value/100) * $this->getAdditiveQuota(), 2);
    }

    /**
     * @inheritDoc
     */
    public function setAdditive(AdditiveInterface $additive)
    {
        $this->additive = $additive;

        $this->updateInfo();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdditive()
    {
        return $this->additive;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    private function updateInfo()
    {
        $this->type = $this->additive->getType();
        $this->name = $this->additive->getName();
        $this->description = $this->additive->getDescription();
        $this->target = $this->additive->getTarget();
        $this->value = $this->additive->getValue();
    }
}
