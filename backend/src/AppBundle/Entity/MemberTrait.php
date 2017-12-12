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
    public function isPlatformExpanse()
    {
        return $this->checkUserRole(UserInterface::ROLE_PLATFORM_EXPANSE);
    }

    /**
     * @inheritDoc
     */
    public function isPlatformLogistic()
    {
       return $this->checkUserRole(UserInterface::ROLE_PLATFORM_LOGISTIC);
    }

    /**
     * @inheritdoc
     */
    public function isPlatformFinancing()
    {
        return $this->checkUserRole(UserInterface::ROLE_PLATFORM_FINANCING);
    }

    /**
     * @inheritDoc
     */
    public function getUserType()
    {
        $roles = array(
            UserInterface::ROLE_DEFAULT => 'User',
            UserInterface::ROLE_OWNER => 'Administrador',
            UserInterface::ROLE_OWNER_MASTER => 'Administrador',
            UserInterface::ROLE_PLATFORM_ADMIN => 'Administrador',
            UserInterface::ROLE_PLATFORM_MASTER => 'Master',
            UserInterface::ROLE_PLATFORM_COMMERCIAL => 'Commercial',
            UserInterface::ROLE_PLATFORM_FINANCIAL => 'Financial',
            UserInterface::ROLE_PLATFORM_AFTER_SALES => 'Pós-Venda',
            UserInterface::ROLE_PLATFORM_EXPANSE => 'Expansão',
            UserInterface::ROLE_PLATFORM_LOGISTIC => 'Logística',
            UserInterface::ROLE_PLATFORM_FINANCING => 'Financiamento'
        );

        if (!$this->user instanceof UserInterface || !$this->user->getRoles()) {
                return $roles['ROLE_DEFAULT'];
        }

        $userRoles = $this->user->getRoles();
        $mainRole = $userRoles[0];

        return $roles[$mainRole];
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
