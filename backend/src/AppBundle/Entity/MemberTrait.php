<?php

namespace AppBundle\Entity;

/**
 * Class MemberTrait
 * @property UserInterface $user
 */
trait MemberTrait
{
    /**
     * @inheritDoc
     */
    public function isPlatformUser()
    {
        if($this->user instanceof UserInterface)
            return $this->user->isPlatform();

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isPlatformMaster()
    {
        return $this->checkUserRole(UserInterface::ROLE_PLATFORM_MASTER);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformAdmin()
    {
        return $this->checkUserRole(UserInterface::ROLE_PLATFORM_ADMIN);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformCommercial()
    {
        return $this->checkUserRole(UserInterface::ROLE_PLATFORM_COMMERCIAL);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformFinancial()
    {
        return $this->checkUserRole(UserInterface::ROLE_PLATFORM_FINANCIAL);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformAfterSales()
    {
        return $this->checkUserRole(UserInterface::ROLE_PLATFORM_AFTER_SALES);
    }

    /**
     * @inheritDoc
     */
    public function getUserType()
    {
        $type = 'None';

        if ($this->user instanceof UserInterface){

            $roles = $this->user->getRoles();
            
            if (array_key_exists(0, $roles)) {
                switch ($roles[0]) {
                    case User::ROLE_DEFAULT:
                        $type = 'User';
                        break;
                    case User::ROLE_OWNER:
                    case User::ROLE_OWNER_MASTER:
                    case User::ROLE_PLATFORM_ADMIN:
                        $type = 'Administrator';
                        break;
                    case User::ROLE_PLATFORM_MASTER:
                        $type = 'Master';
                        break;
                    case User::ROLE_PLATFORM_COMMERCIAL:
                        $type = 'Commercial';
                        break;
                    case User::ROLE_PLATFORM_FINANCIAL:
                        $type = 'Financial';
                        break;
                    case User::ROLE_PLATFORM_AFTER_SALES:
                        $type = 'Pós-Venda';
                        break;
                    default:
                        $type = 'User';
                        break;
                }
            }
        }

        return $type;
    }

    /**
     * @param $role
     * @return bool
     */
    private function checkUserRole($role)
    {
        $this->ensureMember();

        return $this->user ? $this->user->hasRole($role) : false;
    }

    /**
     * Ensure called context is member instance
     */
    private function ensureMember()
    {
        $this->ensureContext(Customer::CONTEXT_MEMBER);
    }
}
