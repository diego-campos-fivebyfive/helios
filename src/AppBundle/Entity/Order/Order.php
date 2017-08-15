<?php

namespace AppBundle\Entity\Order;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="app_order")
 * @ORM\Entity
 */
class Order implements OrderInterface
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $account;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Element", mappedBy="order", cascade={"persist", "remove"})
     */
    private $elements;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param integer $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getTotal()
    {
        $total = 0;
        foreach ($this->elements as $element){
            $total += $element->getTotal();
        }

        return $total;
    }

    /**
     * @inheritDoc
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @inheritDoc
     */
    public function addElement(ElementInterface $element)
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);

            if (!$element->getOrder()) $element->setOrder($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeElement(ElementInterface $element)
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getElements()
    {
        return $this->elements;
    }

}

