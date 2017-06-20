<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Team Interface
 */
interface TeamInterface
{

    const DISABLED = 0;
    const ENABLED = 1;

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function __toString();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Team
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set enabled
     *
     * @param integer $enabled
     *
     * @return Team
     */
    public function setEnabled($enabled);

    /**
     * Get enabled
     *
     * @return int
     */
    public function getEnabled();

    /**
     * @return null
     */
    public function prePersist();

    /**
     * @return null
     */
    public function preUpdate();

    /**
     * Get customer
     *
     * @return BusinessInterface
     */
    public function getAccount();

    /**
     * @param BusinessInterface $account
     * @return TeamInterface
     */
    public function setAccount(BusinessInterface $account);

    /**
     * @param BusinessInterface $member
     * @return TeamInterface
     */
    public function addMember(BusinessInterface $member);

    /**
     * @param BusinessInterface $member
     * @return TeamInterface
     */
    public function removeMember(BusinessInterface $member);

    /**
     * Get Members
     *
     * @return ArrayCollection
     */
    public function getMembers();

    /**
     * @return BusinessInterface
     */
    public function getLeader();
}
