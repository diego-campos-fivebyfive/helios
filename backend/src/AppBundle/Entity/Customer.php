<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserInterface as FOSUser;
use Kolina\CustomerBundle\Entity\Customer as AbstractCustomer;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Customer
 *
 * @ORM\Table(name="app_customer")
 * @ORM\Entity
 */
class Customer extends AbstractCustomer
    implements
    BusinessInterface,
    AccountInterface,
    MemberInterface,
    CustomerInterface,
    ContactInterface,
    CompanyInterface
{
    use TokenizerTrait;
    use ORMBehaviors\SoftDeletable\SoftDeletable;
    use AccountTrait;
    use MemberTrait;
    use CompanyTrait;

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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=15, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="information", type="text", nullable=true)
     */
    private $information;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="ranking", type="integer", nullable=true)
     */
    private $ranking;

    /**
     * @var bool
     *
     * @ORM\Column(name="persistent", type="boolean", nullable=true)
     */
    private $persistent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_leader", type="boolean", nullable=true)
     */
    private $isLeader;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="extraDocument", type="string", nullable=true)
     */
    protected $extraDocument;


    /**
     * @var json
     *
     * @ORM\Column(name="attributes", type="json", nullable=true)
     */
    private $attributes;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", length=200, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var string
     *
     * @ORM\Column(name="office", type="string", nullable=true)
     */
    private $office;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=100, nullable=true)
     */
    private $timezone;

    /**
     * @var array
     *
     * @ORM\Column(name="coordinates", type="array", nullable=true)
     */
    private $coordinates;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_member", type="integer", nullable=true)
     */
    private $maxMember;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expire_at", type="datetime", nullable=true)
     */
    private $expireAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activatedAt;

    /**
     * @var UserInterface
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="info", cascade={"persist"})
     * @ORM\JoinColumn(name="user")
     */
    protected $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Customer", mappedBy="member")
     */
    private $contacts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Task", mappedBy="author")
     */
    private $authoredTasks;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Task", mappedBy="contact")
     */
    private $contactTasks;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Category", mappedBy="account")
     * @ORM\OrderBy({
     *     "position"="ASC"
     * })
     */
    private $categories;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="contacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     * })
     */
    private $member;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="members")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     * })
     */
    private $account;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="employees")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

    /**
     * @var \AppBundle\Entity\Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    private $category;

    /**
     * @var \AppBundle\Entity\Context
     *
     * @ORM\Column()
     */
    private $context;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Task", mappedBy="members")
     */
    private $assignedTasks;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Customer", inversedBy="alloweds")
     * @ORM\JoinTable(name="app_accessors",
     *   joinColumns={
     *     @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="accessor_id", referencedColumnName="id")
     *   }
     * )
     */
    private $accessors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Customer", mappedBy="accessors")
     */
    private $alloweds;

    private $edition = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->employees = new ArrayCollection();
        $this->authoredTasks = new ArrayCollection();
        $this->contactTasks = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->assignedTasks = new ArrayCollection();
        $this->accessors = new ArrayCollection();
        $this->alloweds = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->childAccounts = new ArrayCollection();

        $this->coordinates = [];
        $this->attributes = [];
        $this->status = self::PENDING;
    }

    function __toString()
    {
        return $this->getName();
    }

    public function getId()
    {
        return $this->id;
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
    public function setTitle($title)
    {
        if ($this->isCompany() || $this->isAccount())
            $this->unsupportedContextException();

        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setInformation($information)
    {
        $this->information = $information;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInformation()
    {
        return $this->information;
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
    public function isAccount()
    {
        return $this->context ? $this->context == self::CONTEXT_ACCOUNT : false;
    }

    /**
     * @inheritDoc
     */
    public function isMember()
    {
        return $this->context == self::CONTEXT_MEMBER;
    }

    /**
     * @inheritDoc
     */
    public function isPerson()
    {
        return $this->context == self::CONTEXT_PERSON;
    }

    /**
     * @inheritDoc
     */
    public function isCompany()
    {
        return $this->context == self::CONTEXT_COMPANY;
    }

    /**
     * @inheritDoc
     */
    public function isContact()
    {
        return $this->isCompany() || $this->isPerson();
    }

    /**
     * @inheritDoc
     */
    public function isOnline()
    {
        return $this->getUser()->isOnline();
    }

    /**
     * @inheritDoc
     */
    public function isInvited()
    {
        if(!$this->isMember())
            $this->unsupportedContextException();

        return null != $this->getConfirmationToken() && !$this->getUser();
    }

    /**
     * @inheritDoc
     */
    public function setAccount(AccountInterface $account)
    {
        if (!$account->isAccount())
            $this->unsupportedContextException();

        $this->account = $account;
            $account->addMember($this);

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
    public function getOwners()
    {
        if (!$this->isAccount())
            $this->unsupportedContextException();

        $owners = $this->members->filter(function (BusinessInterface $member) {
            $user = $member->getUser();
            if($user instanceof UserInterface){
                return  $user->isOwner();
            }
            return false;
        });

        return $this->edition ? new ArrayCollection([$owners->first()]) : $owners;
    }

    /**
     * @inheritDoc
     */
    public function setCompany(BusinessInterface $company = null)
    {
        if (($company instanceof BusinessInterface && !$company->isCompany()) || !$this->isPerson())
            $this->unsupportedContextException();

        $this->company = $company;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @inheritDoc
     */
    public function isEmployee()
    {
        //return $this->type == self::TYPE_EMPLOYEE || $this->type == self::TYPE_OWNER;
    }


    /**
     * @inheritDoc
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param //MediaInterface $media
     * @return UserInterface
     */
    /*public function setMedia(MediaInterface $media)
    {
        $this->media = $media;

        return $this;
    }*/

    /**
     * @return //MediaInterface
     */
    /*public function getMedia()
    {
        return $this->media;
    }*/

    /**
     * @inheritDoc
     */
    public function setCategory(CategoryInterface $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @inheritDoc
     */
    public function getFirstname($force = false)
    {
        if(!$force)
            return $this->firstname;

        $names = explode(' ', $this->firstname);

        return $names[0];
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        $this->status = $status;

        if(self::ACTIVATED == $status)
            $this->activatedAt = new \DateTime;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setExtraDocument($extraDocument)
    {
        $this->extraDocument = $extraDocument;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtraDocument()
    {
        return $this->extraDocument;
    }



    /**
     * @inheritDoc
     */
    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAttribute($key)
    {
        if($this->hasAttribute($key)){
            unset($this->attributes[$key]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($key, $default = null)
    {
        if($this->hasAttribute($key)){
            return $this->attributes[$key];
        }
        return $default;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function setMember(BusinessInterface $member)
    {
        /*if (!$this->isContact() || !$member->isMember())
            $this->unsupportedContextException();*/

        $this->member = $member;
    }

    /**
     * @inheritDoc
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Caution: This is a several reverse definition
     * @param null $isOwner
     * @return $this|bool
     */
    public function isOwner($isOwner = null)
    {
        if (is_null($isOwner)) {
            if ($this->user instanceof UserInterface) {
                return $this->user->isOwner();
            }

            return false;
        }

        if ($isOwner)
            $this->getUser()->addRole(UserInterface::ROLE_OWNER);
        else
            $this->getUser()->removeRole(UserInterface::ROLE_OWNER);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isMasterOwner()
    {
        if(!$this->isMember()) {
            $this->unsupportedContextException();
        }

        if($this->user){
            return $this->user->hasRole(UserInterface::ROLE_OWNER_MASTER);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isAdmin()
    {
        if ($this->user instanceof UserInterface) {
            return $this->user->isAdmin();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        $owner = $this->members->filter(function (BusinessInterface $member) {
            return $member->isMasterOwner();
        })->first();

        if (!$owner) {
            $owners = $this->getOwners();
            $owner = $owners[0];
        }

        if (!$owner) {
            $owner = $this->members->first();
        }

        return $owner;
    }

    /**
     * @inheritDoc
     */
    public function addContact(BusinessInterface $contact)
    {
        if (!$this->contacts->contains($contact)) {
            $contact->setMember($this);
            $this->contacts->add($contact);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeContact(BusinessInterface $contact)
    {
        if ($this->contacts->contains($contact))
            $this->contacts->removeElement($contact);

        return $this;
    }

    /**
     * @inheritDoc
     */
    function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @inheritDoc
     */
    public function getAccountContacts()
    {
        if (!$this->isMember() && !$this->isAccount())
            $this->unsupportedContextException();

        $account = $this->isAccount() ? $this : $this->getAccount();

        $accountContacts = new ArrayCollection();
        foreach ($account->getMembers() as $member) {

            $memberContacts = $member->getContacts();
            foreach ($memberContacts as $memberContact) {

                if (!$accountContacts->contains($memberContact) && !$memberContact->isDeleted()) {
                    $accountContacts->add($memberContact);
                }
            }
        }

        return $accountContacts;
    }

    /**
     * @inheritDoc
     */
    public function getAllowedContacts()
    {
        if (!$this->isMember())
            $this->unsupportedContextException();

        if ($this->isOwner())
            return $this->getAccountContacts();

        $allowedContacts = $this->contacts->filter(function (BusinessInterface $contact){
            return !$contact->isDeleted();
        });

        foreach ($this->getAlloweds() as $allowed) {
            if (!$allowedContacts->contains($allowed) && !$allowed->isDeleted()) {
                $allowedContacts->add($allowed);
            }
        }

        return $allowedContacts;
    }

    /**
     * @inheritDoc
     */
    public function getAllowedCompanies()
    {
        return $this->getAllowedContacts()->filter(function (BusinessInterface $contact) {
            return $contact->isCompany();
        });
    }

    /**
     * @inheritDoc
     */
    public function getAllowedPersons()
    {
        return $this->getAllowedContacts()->filter(function (BusinessInterface $contact) {
            return $contact->isPerson();
        });
    }

    /**
     * @inheritDoc
     */
    public function isLeader($isLeader = null)
    {
        if (is_null($isLeader))
            return $this->isLeader;

        $this->isLeader = $isLeader;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContext($context)
    {
        $this->context = $context;

        if($this->isAccount()){
            $this->maxMember = self::MAX_MEMBERS;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function addAccessor(BusinessInterface $accessor)
    {
        if (!$this->accessors->contains($accessor)) {
            $this->accessors->add($accessor);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAccessor(BusinessInterface $accessor)
    {
        if ($this->accessors->contains($accessor)) {
            $this->accessors->removeElement($accessor);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccessors()
    {
        return $this->accessors;
    }

    /**
     * @inheritDoc
     */
    public function isAccessibleBy(BusinessInterface $member)
    {
        if(!$member->isMember()){
            $this->unsupportedContextException();
        }

        if($member->isOwner() || $this->member->getId() === $member->getId()){
            return true;
        }

        $accessor = $this->accessors->filter(function(BusinessInterface $accessor) use($member){
            return $accessor->getId() === $member->getId();
        })->first();

        return $accessor instanceof BusinessInterface;
    }

    /**
     * @inheritDoc
     */
    public function getAlloweds()
    {
        return $this->alloweds;
    }

    /**
     * @inheritDoc
     */
    public function addClassification(CategoryInterface $classification)
    {
        if (!$this->classifications->contains($classification)) {
            $this->classifications->add($classification);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeClassification(CategoryInterface $classification)
    {
        if ($this->classifications->contains($classification)) {
            $this->classifications->removeElement($classification);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getClassifications()
    {
        return $this->classifications;
    }

    /**
     * @inheritDoc
     */
    public function addCategory(CategoryInterface $category)
    {
        $this->ensureAccount();

        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeCategory(CategoryInterface $category)
    {
        $this->ensureAccount();

        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCategories($context = null)
    {
        $this->ensureAccount();

        if($context){
            return $this->categories->filter(function(CategoryInterface $category) use($context){
                if($context instanceof ContextInterface) {
                    $context = $context->getId();
                }
                return $category->getContext()->getId() === $context;
            });
        }

        return $this->categories;
    }

    /**
     * @inheritDoc
     */
    public function getSaleCycles()
    {
        return $this->getCategories()->filter(function(\AppBundle\Entity\CategoryInterface $category){
            return $category->getContext()->getId() == Category::CONTEXT_SALE_STAGE;
        });
    }

    /**
     * @inheritDoc
     */
    public function setUser(FOSUser $user)
    {
        if (!$this->isMember())
            throw new \InvalidArgumentException('Users are bound only to the member context');

        return parent::setUser($user);
    }

    /**
     * @inheritDoc
     */
    public static function getTitleList()
    {
        return [
            'Sr' => 'Sr',
            'Sra' => 'Sra'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getStatusList()
    {
        return [
            self::PENDING => 'Pendente',
            self::STANDING => 'Email verificado',
            self::APPROVED => 'Conta confirmada',
            self::ACTIVATED => 'Ativo',
            self::LOCKED => 'Bloqueado',
            self::REFUSED => 'Recusado'
        ];
    }

    /**
     * @inheritDoc
     */
    public function setCoordinates(array $coordinates = [])
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @inheritDoc
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->street;
    }

    /**
     * @inheritDoc
     */
    public function getLatitude()
    {
        return $this->getCoordinate('latitude');
    }

    /**
     * @inheritDoc
     */
    public function getLongitude()
    {
        return $this->getCoordinate('longitude');
    }

    /**
     * @inheritDoc
     */
    public function getCoordinate($coordinate)
    {
        if(array_key_exists($coordinate, $this->coordinates)){
            return $this->coordinates[$coordinate];
        }

        return null;
    }

    public function setConfirmationToken($confirmationToken = null)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @inheritDoc
     */
    public function prePersist()
    {
        parent::prePersist();
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        parent::preUpdate();
        $this->generateToken();
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function unsupportedContextException()
    {
        throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_CONTEXT);
    }

    /**
     * @inheritDoc
     */
    public function addAuthoredTask(TaskInterface $task)
    {
        if(!$this->isMember())
            $this->unsupportedContextException();

        if(!$this->authoredTasks->contains($task)){
            $this->authoredTasks->add($task);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAuthoredTask(TaskInterface $task)
    {
        if($this->authoredTasks->contains($task)){
            $this->authoredTasks->removeElement($task);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthoredTasks()
    {
        return $this->authoredTasks;
    }

    /**
     * @inheritDoc
     */
    public function addContactTask(TaskInterface $task)
    {
        if(!$this->isContact())
            $this->unsupportedContextException();

        if(!$this->contactTasks->contains($task)){
            $this->contactTasks->add($task);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeContactTask(TaskInterface $task)
    {
        if($this->contactTasks->contains($task)){
            $this->contactTasks->removeElement($task);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContactTasks()
    {
        return $this->contactTasks;
    }

    /**
     * @inheritDoc
     */
    public function addAssignedTask(TaskInterface $task)
    {
        if(!$this->isMember())
            $this->unsupportedContextException();

        if(!$this->assignedTasks->contains($task)){
            $this->assignedTasks->add($task);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAssignedTask(TaskInterface $task)
    {
        if($this->assignedTasks->contains($task)){
            $this->assignedTasks->removeElement($task);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAssignedTasks()
    {
        return $this->assignedTasks;
    }

    /**
     * @inheritDoc
     */
    public function getEmailAccounts()
    {
        return $this->emailAccounts;
    }

    /**
     * TODO: Necessary to implement conversion mechanism
     * @inheritDoc
     */
    public function isCustomer()
    {
        return $this->isCompany() || $this->isPerson();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
            'context' => $this->context,
            'title' => $this->title,
            'office' => $this->office ?: '',
            'firstname' => $this->firstname ?: '',
            'lastname' => $this->lastname ?: '',
            'name' => $this->getName() ?: '',
            'email' => $this->email ?: '',
            'document' => $this->document ?: '',
            'extraDocument' => $this->extraDocument ?: '',
            'mobile' => $this->mobile ?: '',
            'phone' => $this->phone ?: '',
            'fax' => $this->fax ?: '',
            'website' => $this->website ?: '',
            'information' => $this->information ?:'',
            'postcode' => $this->postcode ?: '',
            'country' => $this->country ?: '',
            'state' => $this->state ?: '',
            'city' => $this->city ?: '',
            'district' => $this->district ?: '',
            'street' => $this->street ?: '',
            'number' => $this->number ?: '',
            'complement' => $this->complement ?: '',
            'coordinates' => $this->coordinates,
            'attributes' => $this->attributes,
            'address' => $this->getAddress(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDaysToExpire()
    {
        /*if($this->expireAt instanceof \DateTime){

            return date_diff(new \DateTime(), $this->expireAt)->d;
        }

        return 0;*/

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isTrailAccount()
    {
        if($this->isAccount()) {
            $signature = $this->getOwner()->getSignature();
            return $this->expireAt instanceof \DateTime && !$signature['subscription'];
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function setExpireAt(\DateTime $expireAt = null)
    {
        if(!$this->isAccount()){
            $this->unsupportedContextException();
        }

        $this->expireAt = $expireAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpireAt()
    {
        if(!$this->isAccount()){
            $this->unsupportedContextException();
        }

        return $this->expireAt;
    }

    /**
     * @inheritDoc
     */
    public function setActivatedAt(\DateTime $activatedAt)
    {
        $this->activatedAt = $activatedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getActivatedAt()
    {
        return $this->activatedAt;
    }

    /**
     * @inheritDoc
     */
    public function isExpired()
    {
        if($this->isAccount() && $this->expireAt instanceof \DateTime){

            $today = new \DateTime();

            return $today->getTimestamp() > $this->expireAt->getTimestamp();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getSignature()
    {
        if($this->isAccount()){
            return $this->getOwner()->getSignature();
        }

        if(!$this->isMasterOwner()){
            throw new \InvalidArgumentException('Invalid requester');
        }

        $signature = $this->getAttribute('signature', [
            'customer' => null,
            'subscription' => null,
            'payment_profile' => null
        ]);

        return $signature;
    }

    /**
     * @inheritDoc
     */
    public function ensureAccount()
    {
        if(!$this->isAccount()){
            $this->unsupportedContextException();
        }
    }

    protected function ensureContext($context)
    {
        if($this->context != $context){
            $this->unsupportedContextException();
        }
    }

    /**
     * @inheritDoc
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @inheritDoc
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * @inheritDoc
     */
    public function addRanking($ranking)
    {
        $this->ranking += $ranking;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPersistent()
    {
        return $this->persistent;
    }


    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}

