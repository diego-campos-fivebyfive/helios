<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\ClassificationBundle\Entity\BaseCategory;

/**
 * Category
 *
 * @ORM\Table(name="app_category")
 * @ORM\Entity
 */
class Category extends BaseCategory implements CategoryInterface
{
    use TokenizerTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Category", mappedBy="parent")
     */
    protected $children;

    /**
     * @var \AppBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent", referencedColumnName="id")
     * })
     */
    protected $parent;

    /**
     * @var \AppBundle\Entity\Context
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Context")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="context", referencedColumnName="id")
     * })
     */
    protected $context;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="categories")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     * })
     */
    private $account;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->enabled = true;
    }

    /**
     * @inheritDoc
     */
    public function getSortedName()
    {
        return (string) $this->position . ' - ' . $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return BusinessInterface
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param BusinessInterface $account
     * @return Category
     */
    public function setAccount(BusinessInterface $account)
    {
        $account->ensureAccount();

        $this->account = $account;
        return $this;
    }

    public function prePersist()
    {
        $this->generateToken();
        parent::prePersist();
    }
}

