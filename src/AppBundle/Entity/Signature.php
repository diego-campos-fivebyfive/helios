<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Signature
 *
 * @ORM\Table(name="app_signature")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Signature implements SignatureInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var json
     *
     * @ORM\Column(name="content", type="json")
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_id", referencedColumnName="id", unique=true)
     * })
     */
    private $account;


    function __construct()
    {
        $this->log = [];
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setAccount(BusinessInterface $account)
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
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @inheritDoc
     */
    public function setBillId($billId)
    {
        $this->billId = $billId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBillId()
    {
        return $this->billId;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->content[$content->id] = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function prePersist()
    {
        $this->generateToken();
    }
}

