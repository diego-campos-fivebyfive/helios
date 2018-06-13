<?php

namespace AppBundle\Entity\Kit;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart Pool
 * @ORM\Entity
 * @ORM\Table(name="app_cart_pool")
 * @ORM\HasLifecycleCallbacks()
 */
class CartPool
{
    const STATUS_CREATED = 0;

    const STATUS_BILLET_PENDING = 1;
    const STATUS_BILLET_PAID = 2;
    const STATUS_BILLET_CANCELED = 3;

    const STATUS_CARD_PENDING = 4;
    const STATUS_CARD_CANCELED = 5;
    const STATUS_CARD_APPROVED = 6;
    const STATUS_CARD_DENIED = 7;
    const STATUS_CARD_AUTHORIZED = 8;

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
    private $code;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $account;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $items;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $callbacks;

    /**
     * @var array
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $checkout;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $billetId;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->account = [];
        $this->items = [];
        $this->callbacks = [];
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
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return Customer|array
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param $account
     * @return $this
     */
    public function setAccount(AccountInterface $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

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

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array $callbacks
     * @return $this
     */
    public function setCallbacks(array $callbacks = [])
    {
        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @param array $callback
     */
    public function addCallback(array $callback)
    {
        $this->callbacks[] = $callback;
    }

    /**
     * @return string
     */
    public function getBilletId()
    {
        return $this->billetId;
    }

    /**
     * @param string $billetId
     * @return CartPool
     */
    public function setBilletId($billetId)
    {
        $this->billetId = $billetId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
    }

    /**
     * @return array
     */
    public static function getCardStatuses()
    {
        return [
            'PENDING' => self::STATUS_CARD_PENDING,
            'CANCELED' => self::STATUS_CARD_CANCELED,
            'APPROVED' => self::STATUS_CARD_APPROVED,
            'DENIED' => self::STATUS_CARD_DENIED,
            'AUTHORIZED' => self::STATUS_CARD_AUTHORIZED
        ];
    }

    /**
     * @return array
     */
    public static function getBilletStatuses()
    {
        return [
            'PENDING' => self::STATUS_BILLET_PENDING,
            'PAID' => self::STATUS_BILLET_PAID,
            'CANCELED' => self::STATUS_BILLET_CANCELED
        ];
    }

    /**
     * @param bool $keys
     * @return array
     */
    public static function getStatusNames($keys = false)
    {
        $statuses =  [
            self::STATUS_CREATED => 'criado',
            self::STATUS_BILLET_PENDING => 'boleto - pendente',
            self::STATUS_BILLET_PAID => 'boleto - pago',
            self::STATUS_BILLET_CANCELED => 'boleto - cancelado',
            self::STATUS_CARD_PENDING => 'cartão - pendente',
            self::STATUS_CARD_CANCELED => 'cartão - cancelado',
            self::STATUS_CARD_APPROVED => 'cartão - aprovado',
            self::STATUS_CARD_DENIED => 'cartão - negado',
            self::STATUS_CARD_AUTHORIZED => 'cartão - autorizado'
        ];

        return $keys ? array_keys($statuses) : $statuses ;
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return self::getStatusNames()[$this->status];
    }

}
