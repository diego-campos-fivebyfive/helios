<?php

namespace AppBundle\Entity;

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
    public function getUserType()
    {
        if ($this->user instanceof UserInterface){

            $roles = $this->user->getRoles();

            switch ($roles[0]){
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
                default:
                    $type = 'User';
                    break;
            }

            return $type;
        }

        return 'None';
    }
}
