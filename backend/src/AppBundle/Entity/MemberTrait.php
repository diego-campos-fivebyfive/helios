<?php

namespace AppBundle\Entity;

trait MemberTrait
{
    /**
     * @return bool
     */
    public function isPlatformUser()
    {
        if($this->user instanceof UserInterface)
            return $this->user->isPlatform();

        return false;
    }
}
