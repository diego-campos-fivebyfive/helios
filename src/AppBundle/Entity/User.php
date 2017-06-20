<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as AbstractUser;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletable;
use Kolina\CustomerBundle\Entity\InfoTrait;

/**
 * User
 *
 * @ORM\Table(name="app_user")
 * @ORM\Entity
 */
class User extends AbstractUser implements UserInterface
{
    use InfoTrait;
    use SoftDeletable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_activity", type="datetime", nullable=true)
     */
    private $lastActivity;

    /**
     * @var BusinessInterface
     * @ORM\OneToOne(targetEntity="Customer", mappedBy="user")
     */
    protected $info;

    public function __construct()
    {
        parent::__construct();
        $this->enabled = true;
    }

    /**
     * @inheritDoc
     */
    public function isSame(UserInterface $user)
    {
        return $this->id == $user->getId();
    }

    /**
     * @inheritDoc
     */
    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * @inheritDoc
     */
    public function isOwner()
    {
        return $this->hasRole(self::ROLE_OWNER);
    }

    public function isOwnerMaster()
    {
        return $this->hasRole(self::ROLE_OWNER_MASTER);
    }

    /**
     * @inheritDoc
     */
    public function setLastActivity(\DateTime $lastActivity)
    {
        $this->lastActivity = $lastActivity;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @inheritDoc
     */
    public function isOnline($status = false)
    {
        if(!$this->lastActivity instanceof \DateTime){
            return false;
        }

        $delay = new \DateTime(self::ACTIVITY_DELAY);

        $lastActivity = (int) preg_replace('/\D/','', $this->lastActivity->format('Y-m-d H:i:s'));
        $delayActivity = (int) preg_replace('/\D/','', $delay->format('Y-m-d H:i:s'));

        $online = $lastActivity > $delayActivity;

        return !$status ? $online : ($online ? 'online' : 'offline') ;
    }
}

