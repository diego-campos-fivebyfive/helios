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
     * @param $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->ensureContext(Customer::CONTEXT_ACCOUNT);

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

        return null == $signature['subscription'] || $this->getStatus() == self::STATUS_LOCKED;
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
}