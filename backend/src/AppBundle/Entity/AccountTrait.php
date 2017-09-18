<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This trait solve AccountInterface methods
 */
trait AccountTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $level;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="account", cascade={"persist", "remove"})
     */
    protected $members;

    /**
     * @param $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->ensureAccount();

        $this->level = $level;

        return $this;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @inheritDoc
     */
    public function setMaxMember($maxMember)
    {
        $this->ensureAccount();

        $this->maxMember = (int) $maxMember;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMaxMembers()
    {
        $this->ensureAccount();

        return $this->maxMember ? $this->maxMember : self::MAX_MEMBERS;
    }

    /**
     * @inheritDoc
     */
    public function addMember(BusinessInterface $member)
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);

            if (!$member->getAccount())
                $member->setAcccount();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeMember(BusinessInterface $member)
    {
        if ($this->members->contains($member))
            $this->members->removeElement($member);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMembers()
    {
        $this->ensureAccount();

        return $this->members->filter(function(BusinessInterface $member){
            return !$member->isDeleted();
        });
    }

    /**
     * @inheritDoc
     */
    public function getActiveMembers()
    {
        $this->ensureAccount();

        return $this->members->filter(function(MemberInterface $member){
            return $member->getUser()->getLastActivity() instanceof \DateTime;
        });
    }

    /**
     * @inheritDoc
     */
    public function getInactiveMembers()
    {
        $this->ensureAccount();

        return $this->members->filter(function(MemberInterface $member){
            return $member->getUser() instanceof UserInterface && $member->isDeleted();
        });
    }

    /**
     * @inheritDoc
     */
    public function getInvitedMembers()
    {
        $this->ensureAccount();

        return $this->members->filter(function(MemberInterface $member){
            return $member->getUser()->getConfirmationToken() && !$member->isDeleted();
        });
    }

    /**
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->status == self::CONFIRMED;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if(!is_string($key) || is_numeric($key))
            throw new \InvalidArgumentException('Invalid attribute key type. Type allowed: string');

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isFreeAccount()
    {
        $signature = $this->getSignature();

        return null == $signature['subscription'] || $this->getStatus() == self::LOCKED;
    }

    /**
     * @inheritDoc
     */
    public function getProjectsCount()
    {
        return $this->getAttribute(self::ATTR_PROJECTS_COUNT, 0);
    }

    /**
     * @inheritDoc
     */
    public function getProjectsQuota()
    {
        return $this->getAttribute(self::ATTR_PROJECTS_QUOTA, self::PROJECTS_QUOTA);
    }

    /**
     * @inheritDoc
     */
    public function projectsQuotaIsReached()
    {
        if($this->isFreeAccount()){
            return $this->getProjectsCount() >= $this->getProjectsQuota();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function incrementProjectsCount($count = 1)
    {
        $this->setAttribute(self::ATTR_PROJECTS_COUNT, $this->getProjectsCount() + $count);

        return $this;
    }

    /**
     * Ensure called context is account instance
     */
    private function ensureAccount()
    {
        $this->ensureContext(Customer::CONTEXT_ACCOUNT);
    }
}
