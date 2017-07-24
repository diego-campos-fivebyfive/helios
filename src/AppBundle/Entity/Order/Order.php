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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Component\Project", mappedBy="order", cascade={"persist"})
     */
    private $projects;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return ArrayCollection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param ArrayCollection $projects
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }
}

