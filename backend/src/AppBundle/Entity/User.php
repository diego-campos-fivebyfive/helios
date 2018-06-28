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

    public function accountActivated()
    {
        if($this->info) {
            return $this->info->getAccount()->isActivated();
        }

        return false;
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
    public function isPlatform()
    {
        return self::TYPE_PLATFORM == $this->getType();
    }

    /**
     * @inheritDoc
     */
    public function isPlatformMaster()
    {
        return $this->hasRole(self::ROLE_PLATFORM_MASTER);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformAdmin()
    {
        return $this->hasRole(self::ROLE_PLATFORM_ADMIN);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformCommercial()
    {
        return $this->hasRole(self::ROLE_PLATFORM_COMMERCIAL);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformFinancial()
    {
        return $this->hasRole(self::ROLE_PLATFORM_FINANCIAL);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformAfterSales()
    {
        return $this->hasRole(self::ROLE_PLATFORM_AFTER_SALES);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformExpanse()
    {
        return $this->hasRole(self::ROLE_PLATFORM_EXPANSE);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformLogistic()
    {
        return $this->hasRole(self::ROLE_PLATFORM_LOGISTIC);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformFinancing()
    {
        return $this->hasRole(self::ROLE_PLATFORM_FINANCING);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformBilling()
    {
        return $this->hasRole(self::ROLE_PLATFORM_BILLING);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformExpedition()
    {
        return $this->hasRole(self::ROLE_PLATFORM_EXPEDITION);
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
    public function getType()
    {
        foreach (self::getPlatformRoles() as $platformRole){
            if(in_array($platformRole, $this->getRoles())){
                return self::TYPE_PLATFORM;
            }
        }

        return self::TYPE_ACCOUNT;
    }

    /**
     * @inheritDoc
     */
    public function getRole()
    {
        foreach ($this->getRoles() as $role){
            if(in_array($role, self::getPlatformRoles())){
                return $role;
            }
        }

        return self::ROLE_DEFAULT;
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

    /**
     * This method override default behavior FOSUser
     * Prevent multiple PLATFORM_* roles
     *
     * @param string $role
     * @return $this
     */
    public function addRole($role)
    {
        parent::addRole($role);

        if(count($this->roles) > 1 && $this->isPlatform()) {
            parent::removeRole($this->roles[1]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getRolesOptions() {

        return [
            self::ROLE_PLATFORM_ADMIN => 'Administrador',
            self::ROLE_PLATFORM_COMMERCIAL => 'Comercial',
            self::ROLE_PLATFORM_FINANCIAL => 'Financeiro',
            self::ROLE_PLATFORM_AFTER_SALES => 'Pós-Venda',
            self::ROLE_PLATFORM_EXPANSE => 'Expansão',
            self::ROLE_PLATFORM_LOGISTIC => 'Produção',
            self::ROLE_PLATFORM_FINANCING => 'Financiamento',
            self::ROLE_PLATFORM_BILLING => 'Faturamento',
            self::ROLE_PLATFORM_EXPEDITION => 'Expedição'
        ];
    }

    public static function getAccountGroupRoles()
    {
        return [
            'admin' => self::ROLE_ADMIN,
            'superAdmin' => self::ROLE_SUPER_ADMIN,
            'owner' => self::ROLE_OWNER,
            'ownerMaster' => self::ROLE_OWNER_MASTER
       ];
    }

    /**
     * @inheritDoc
     */
    public static function getPlatformRoles()
    {
        return [
            self::ROLE_PLATFORM_MASTER,
            self::ROLE_PLATFORM_ADMIN,
            self::ROLE_PLATFORM_COMMERCIAL,
            self::ROLE_PLATFORM_FINANCIAL,
            self::ROLE_PLATFORM_AFTER_SALES,
            self::ROLE_PLATFORM_EXPANSE,
            self::ROLE_PLATFORM_LOGISTIC,
            self::ROLE_PLATFORM_FINANCING,
            self::ROLE_PLATFORM_BILLING,
            self::ROLE_PLATFORM_EXPEDITION
        ];
    }

    public static function getPlatformGroupRoles()
    {
        return [
            'admin' => self::ROLE_PLATFORM_ADMIN,
            'afterSales' => self::ROLE_PLATFORM_AFTER_SALES,
            'commercial' => self::ROLE_PLATFORM_COMMERCIAL,
            'expanse' => self::ROLE_PLATFORM_EXPANSE,
            'financial' => self::ROLE_PLATFORM_FINANCIAL,
            'financing' => self::ROLE_PLATFORM_FINANCING,
            'logistic' => self::ROLE_PLATFORM_LOGISTIC,
            'master' => self::ROLE_PLATFORM_MASTER,
            'billing' => self::ROLE_PLATFORM_BILLING,
            'expedition' => self::ROLE_PLATFORM_EXPEDITION,
        ];
    }
}
