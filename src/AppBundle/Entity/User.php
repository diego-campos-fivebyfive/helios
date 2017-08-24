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
     * @var integer
     *
     * @ORM\Column(name="isquik_id", type="integer", nullable=true)
     */
    private $isquik_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_activity", type="datetime", nullable=true)
     */
    private $lastActivity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

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
        return $this->hasRole(self::ROLE_OWNER) || $this->hasRole(self::ROLE_OWNER_MASTER);
    }

    public function isOwnerMaster()
    {
        return $this->hasRole(self::ROLE_OWNER_MASTER);
    }

    /**
     * @inheritDoc
     */
    public function setIsquikId($isquik_id)
    {
        $this->isquik_id = $isquik_id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsquikId()
    {
        return $this->isquik_id;
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

    /**
     * @inheritDoc
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}
