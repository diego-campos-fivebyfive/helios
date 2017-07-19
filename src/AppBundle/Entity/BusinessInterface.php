<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kolina\CustomerBundle\Entity\CustomerInterface as BaseCustomerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;

/**
 * CustomerInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface BusinessInterface extends BaseCustomerInterface
{
    /**
     * ACCOUNT is the first link to the platform
     * ACCOUNT has MEMBERS
     * MEMBER does not exist without ACCOUNT
     */
    const CONTEXT_ACCOUNT = 'account';
    const CONTEXT_MEMBER = 'member';

    /**
     * COMPANY is a contact type belonging to the ACCOUNT
     * PERSON is a contact type belonging to the ACCOUNT
     * Both (COMPANY or PERSON) they are linked to the account through member
     * Both (COMPANY or PERSON) they are considered contact
     * PERSON may or may not be associated with a COMPANY
     * COMPANY has PERSONS
     * PERSON and COMPANY are not without link a MEMBER
     */
    const CONTEXT_COMPANY = 'company';
    const CONTEXT_PERSON = 'person';

    /**
     * Used in categories manager and forms
     */
    const CATEGORY_CONTACT = 'contact_category';

    /**
     * Status values
     */
    const STATUS_CHECKED = 0;
    const STATUS_ENABLED = 1;
    const STATUS_LOCKED  = 2;
    const STATUS_SUSPEND = 4;

    /**
     * Token support
     */
    const TOKEN_ENTROPY = 200;
    const ERROR_UNSUPPORTED_CONTEXT = 'The context is not supported';

    /**
     * @param $type
     * @return int
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getType();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @return bool
     */
    public function isAccount();

    /**
     * @return bool
     */
    public function isMember();

    /**
     * @return bool
     */
    public function isOwner();

    /**
     * @return bool
     */
    public function isMasterOwner();

    /**
     * @return bool
     */
    public function isAdmin();

    /**
     * @return bool
     */
    public function isPerson();

    /**
     * @return bool
     */
    public function isCompany();

    /**
     * @return bool
     */
    public function isContact();

    /**
     * @return bool
     */
    public function isOnline();

    /**
     * @return bool
     */
    public function isInvited();

    /**
     * @param $status
     * @return BusinessInterface
     */
    public function setStatus($status);

    /**
     * @return int | string
     */
    public function getStatus();

    /**
     * @param $key
     * @param $value
     * @return BusinessInterface
     */
    public function addAttribute($key, $value);

    /**
     * @param $key
     * @return BusinessInterface
     */
    public function removeAttribute($key);

    /**
     * @param $key
     * @return bool
     */
    public function hasAttribute($key);

    /**
     * @param $key
     * @param null $default
     * @return BusinessInterface
     */
    public function getAttribute($key, $default = null);

    /**
     * @param array $attributes
     * @return BusinessInterface
     */
    public function setAttributes(array $attributes = []);
        
    /**
     * @return array
     */
    public function getAttributes();
    
    /**
     * @param null $confirmationToken
     * @return BusinessInterface
     */
    public function setConfirmationToken($confirmationToken = null);

    /**
     * @return string | null
     */
    public function getConfirmationToken();

    /**
     * @return bool
     */
    public function isLocked();

    /**
     * @param //MediaInterface $media
     * @return UserInterface
     */
    //public function setMedia(MediaInterface $media);

    /**
     * @return //MediaInterface
     */
    //public function getMedia();

    /**
     * @param CategoryInterface $category
     * @return BusinessInterface
     */
    public function setCategory(CategoryInterface $category);

    /**
     * @return CategoryInterface
     */
    public function getCategory();

    /**
     * Account reference in which the user is associated
     *
     * @param BusinessInterface $account
     * @return BusinessInterface
     */
    //public function setAccount(BusinessInterface $account);

    /**
     * @return BusinessInterface
     */
    //public function getAccount();

    /**
     * @param BusinessInterface $member
     * @return BusinessInterface
     */
    public function addMember(BusinessInterface $member);

    /**
     * @param BusinessInterface $member
     * @return BusinessInterface
     */
    public function removeMember(BusinessInterface $member);

    /**
     * @return ArrayCollection
     */
    public function getMembers();

    /**
     * @return ArrayCollection
     */
    public function getActiveMembers();

    /**
     * @return ArrayCollection
     */
    public function getInactiveMembers();

    /**
     * @return ArrayCollection
     */
    public function getInvitedMembers();

    /**
     * @return ArrayCollection
     */
    public function getOwners();

    /**
     * @param BusinessInterface $company
     * @return BusinessInterface
     */
    public function setCompany(BusinessInterface $company);

    /**
     * @return BusinessInterface
     */
    public function getCompany();

    /**
     * @return bool
     */
    public function isEmployee();

    /**
     * User reference registers a contact
     * Used by company or person contexts
     *
     * @param BusinessInterface $member
     * @return BusinessInterface
     */
    public function setMember(BusinessInterface $member);

    /**
     * @return BusinessInterface
     */
    public function getMember();

    /**
     * @return BusinessInterface
     */
    public function getOwner();

    /**
     * @param BusinessInterface $contact
     * @return BusinessInterface
     */
    public function addContact(BusinessInterface $contact);

    /**
     * @param BusinessInterface $contact
     * @return BusinessInterface
     */
    public function removeContact(BusinessInterface $contact);

    /**
     * Returns the list of registered contacts by member
     * @return ArrayCollection
     */
    public function getContacts();

    /**
     * Returns the list of registered contacts in the account
     * - all member contacts
     * @return ArrayCollection
     */
    public function getAccountContacts();

    /**
     * @see CONTEXT_MEMBER
     * @return ArrayCollection
     */
    public function getAllowedContacts();

    /**
     * @see CONTEXT_MEMBER
     * @return ArrayCollection
     */
    public function getAllowedCompanies();

    /**
     * @see CONTEXT_MEMBER
     * @return ArrayCollection
     */
    public function getAllowedPersons();

    /**
     * Access strict by account instance
     * @return ArrayCollection
     */
    public function getKits();

    /**
     * @param $work
     * @return BusinessInterface
     */
    public function setOffice($office);

    /**
     * @return string
     */
    public function getOffice();

    /**
     * @return PackageInterface
     */
    public function getPackage();

    /**
     * @param PackageInterface $package
     * @return CustomerInterface
     */
    public function setPackage(PackageInterface $package);

    /**
     * @see self::CONTEXT_ACCOUNT
     *
     * @param TeamInterface $team
     * @return BusinessInterface
     */
    public function addTeam(TeamInterface $team);

    /**
     * @see self::CONTEXT_ACCOUNT
     *
     * @param TeamInterface $team
     * @return BusinessInterface
     */
    public function removeTeam(TeamInterface $team);

    /**
     * @see self::CONTEXT_ACCOUNT
     *
     * @return ArrayCollection
     */
    public function getTeams();

    /**
     * @see self::CONTEXT_MEMBER
     *
     * @return TeamInterface
     */
    public function getTeam();

    /**
     * @see self::CONTEXT_MEMBER
     *
     * @param TeamInterface $teamsF
     * @return CustomerInterface
     */
    public function setTeam(TeamInterface $team = null);

    /**
     * @param null $isLeader
     * @return BusinessInterface
     */
    public function isLeader($isLeader = null);

    /**
     * Accepted Contexts
     * self::CONTEXT_MEMBER
     *
     * @param TaskInterface $task
     * @return BusinessInterface
     */
    public function addAuthoredTask(TaskInterface $task);

    /**
     * @param TaskInterface $task
     * @return BusinessInterface
     */
    public function removeAuthoredTask(TaskInterface $task);

    /**
     * @return ArrayCollection
     */
    public function getAuthoredTasks();

    /**
     * Accepted Contexts
     * self::CONTEXT_PERSON
     * self::CONTEXT_COMPANY
     *
     * @param TaskInterface $task
     * @return mixed
     */
    public function addContactTask(TaskInterface $task);

    /**
     * @param TaskInterface $task
     * @return BusinessInterface
     */
    public function removeContactTask(TaskInterface $task);

    /**
     * @return ArrayCollection
     */
    public function getContactTasks();

    /**
     * Accepted Contexts
     * self::CONTEXT_MEMBER
     *
     * @param TaskInterface $task
     * @return mixed
     */
    public function addAssignedTask(TaskInterface $task);

    /**
     * @param TaskInterface $task
     * @return BusinessInterface
     */
    public function removeAssignedTask(TaskInterface $task);

    /**
     * @return ArrayCollection
     */
    public function getAssignedTasks();
    
    /**
     * @param $context
     * @return $this
     */
    public function setContext($context);

    /**
     * @return string
     */
    public function getContext();

    /**
     * @param BusinessInterface $accessor
     * @return BusinessInterface
     */
    public function addAccessor(BusinessInterface $accessor);

    /**
     * @param BusinessInterface $accessor
     * @return BusinessInterface
     */
    public function removeAccessor(BusinessInterface $accessor);

    /**
     * Returns the list of members who have access to the contact
     * @return ArrayCollection
     */
    public function getAccessors();

    /**
     * @param BusinessInterface $member
     * @return bool
     */
    public function isAccessibleBy(BusinessInterface $member);

    /**
     * Returns the list of contacts to which the member has access
     * @return ArrayCollection
     */
    public function getAlloweds();

    /**
     * @param CategoryInterface $classification
     * @return BusinessInterface
     */
    public function addClassification(CategoryInterface $classification);

    /**
     * @return ArrayCollection
     */
    public function getClassifications();

    /**
     * @param CategoryInterface $category
     * @return BusinessInterface
     */
    public function addCategory(CategoryInterface $category);

    /**
     * @param CategoryInterface $category
     * @return BusinessInterface
     */
    public function removeCategory(CategoryInterface $category);

    /***
     * @param null $context
     * @return ArrayCollection
     */
    public function getCategories($context = null);

    /**
     * @return ArrayCollection
     */
    public function getSaleCycles();

    /**
     * @param CategoryInterface $classification
     * @return BusinessInterface
     */
    public function removeClassification(CategoryInterface $classification);

    /**
     * @return ArrayCollection
     */
    public function getProjects();

    /**
     * @param $title
     * @return BusinessInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param $information
     * @return BusinessInterface
     */
    public function setInformation($information);

    /**
     * @return string
     */
    public function getInformation();

    /**
     * @return array
     */
    public static function getTitleList();

    /**
     * @return array
     */
    public static function getStatusList();

    /**
     * @return int
     */
    public function getMaxMembers();


    /**
     * @param $maxMember
     * @return BusinessInterface
     */
    public function setMaxMember($maxMember);

    /**
     * Return all real modules
     * @return ArrayCollection
     */
    //public function getModules();

    /**
     * Return all real inverters
     * @return ArrayCollection
     */
    //public function getInverters();

    /**
     * @param $timezone
     * @return BusinessInterface
     */
    public function setTimezone($timezone);

    /**
     * @return string
     */
    public function getTimezone();

    /**
     * @param array $coordinates
     * @return BusinessInterface
     */
    public function setCoordinates(array $coordinates = []);

    /**
     * @return array
     */
    public function getCoordinates();

    /**
     * @param $coordinate
     * @return float
     */
    public function getCoordinate($coordinate);

    /**
     * @return string
     */
    public function getAddress();

    /**
     * @return float
     */
    public function getLatitude();

    /**
     * @return float
     */
    public function getLongitude();

    /**
     * Get EmailAccounts
     * 
     * @return ArrayCollection
     */
    public function getEmailAccounts();

    /**
     * @return EmailAccountInterface
     */
    public function getCurrentEmailAccount();

    /**
     * Determines whether the instance is a valid client
     * @return bool
     */
    public function isCustomer();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return bool
     */
    public function isDeleted();

    /**
     * @return mixed
     */
    public function isTrailAccount();

    /**
     * @return int
     */
    public function getDaysToExpire();

    /**
     * @param null|\DateTime $expireAt
     * @return BusinessInterface
     * @see context:account
     */
    public function setExpireAt(\DateTime $expireAt = null);

    /**
     * @return \DateTime|null
     * @see context:account
     */
    public function getExpireAt();

    /**
     * @return bool
     * @see context:account
     */
    public function isExpired();

    /**
     * @return array
     */
    public function getSignature();

    /**
     * Ensures that whoever called belongs to the account context,
     * if not throws an exception
     */
    public function ensureAccount();
}
