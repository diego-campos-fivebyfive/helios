<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface TaskInterface
{

    const TYPE_UNDEFINED = 0;
    const TYPE_VISITATION = 1;
    const TYPE_REUNION = 2;
    const TYPE_PROPOSAL = 4;
    const TYPE_CALL = 8;
    const TYPE_MAIL = 16;
    
    const STATUS_DISABLED = 1;    
    const STATUS_ENABLED = 2;    
    const STATUS_DONE = 4;    
    const STATUS_DELAY = 8;

    const ERROR_INVALID_CONTEXT = 'Invalid owner context';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @return bool
     */
    public function isPending();

    /**
     * @return bool
     */
    public function isDone();

    /**
     * Returns if
     * @return bool
     */
    //public function isSigned(BusinessInterface $author);

    /**
     * BusinessInterface::CONTEXT_MEMBER
     * @param BusinessInterface $author
     * @return TaskInterface
     */
    public function setAuthor(BusinessInterface $author);

    /**
     * @return BusinessInterface
     */
    public function getAuthor();

    /**
     * BusinessInterface::CONTEXT_COMPANY | BusinessInterface::CONTEXT_PERSON
     * @param BusinessInterface $contact
     * @return TaskInterface
     */
    public function setContact(BusinessInterface $contact);

    /**
     * @return BusinessInterface
     */
    public function getContact();

    /**
     * BusinessInterface::CONTEXT_MEMBER
     * @param BusinessInterface $member
     * @return TaskInterface
     */
    public function addMember(BusinessInterface $member);

    /**
     * @param BusinessInterface $member
     * @return TaskInterface
     */
    public function removeMember(BusinessInterface $member);

    /**
     * @return ArrayCollection
     */
    public function getMembers();

    /**
     * @return mixed
     */
    public function getDescription();

    /**
     * @param $description
     * @return mixed
     */
    public function setDescription($description);

    /**
     * @param \DateTime $startAt
     * @return TaskInterface
     */
    public function setStartAt(\DateTime $startAt);

    /**
     * @return \DateTime
     */
    public function getStartAt();

    /**
     * @param \DateTime $endAt
     * @return TaskInterface
     */
    public function setEndAt(\DateTime $endAt);

    /**
     * @return DateTime
     */
    public function getEndAt();

    /**
     * @return \DateTime
     */
    public function getCreateAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getIconType();

    /**
     * @return string
     */
    public function getTagType();

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getStatuses();

    /**
     * @return string
     */
    public function getStatusTag();

    /**
     * @return array
     */
    public static function getFilterData();

    /**
     * @param $key
     * @return array
     */
    public function getMetadata($key);
    
    /**
     * @param $option
     * @return array
     */
    public static function getFilterChoices($option = null);

    /**
     * @return array
     */
    public static function getTypeIcons($index = false);
}
