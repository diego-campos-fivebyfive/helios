<?php

namespace AppBundle\Entity\Stock;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Product
 *
 * @ORM\Table(name="app_stock_product")
 * @ORM\Entity
 */
class Product implements ProductInterface
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="product", cascade={"persist", "remove"})
     */
    private $transactions;

    /**
     * Product constructor.
     */
    function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->code;
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
    public function getStock()
    {
        $stock = 0;
        foreach ($this->transactions as $transaction){
            $stock += $transaction->getAmount();
        }

        return $stock;
    }

    /**
     * @inheritDoc
     */
    public function addTransaction(TransactionInterface $transaction)
    {
        if(!$this->transactions->contains($transaction)){

            $this->transactions->add($transaction);

            if(!$transaction->getProduct()){
                $transaction->setProduct($this);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeTransaction(TransactionInterface $transaction)
    {
        if($this->transactions->contains($transaction))
            $this->transactions->removeElement($transaction);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
