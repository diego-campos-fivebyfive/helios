<?php

namespace AppBundle\Service\Security;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Customer;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccessVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        return $this->isBearableInstance($subject);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if(!$user instanceof UserInterface){
            return false;
        }

        $method = $this->determineMethod($subject, $attribute);

        if(method_exists($this, $method)){
            return $this->$method($subject, $user);
        }

        return false;
    }

    /**
     * @param BusinessInterface $customer
     * @param UserInterface $user
     * @return bool
     */
    private function canEditCustomer(BusinessInterface $customer, UserInterface $user)
    {
        $member = $user->getInfo();

        if ($member instanceof BusinessInterface){

            // Via member
            if ($member->getId() === $customer->getMember()->getId())
                return true;

            // Via member is account owner
            if ($member->isOwner() && ($customer->getMember()->getAccount()->getId() === $member->getAccount()->getId())) {
                return true;
            }

            // Via accessors
            if ($customer->getAccessors()->contains($member))
                return true;
        }

        return false;
    }

    /**
     * @param CategoryInterface $category
     * @param User $user
     * @return bool
     */
    private function canEditCategory(CategoryInterface $category, User $user)
    {
        /** @var BusinessInterface $member */
        $member = $user->getInfo();
        $account = $member->getAccount();

        if($account->getId() === $category->getAccount()->getId()){
            return true;
        }

        return false;
    }

    /**
     * @param $object
     * @return bool
     */
    private function isBearableInstance($object)
    {
        $target = $this->determineTarget($object);

        return in_array($target, [
            'Customer',
            'Category'
        ]);
    }

    /**
     * @param $subject
     * @param $attribute
     * @return string
     */
    private function determineMethod($subject, $attribute)
    {
        return sprintf('can%s%s', ucfirst($attribute), $this->determineTarget($subject));
    }

    /**
     * @return mixed
     */
    private function determineTarget($subject)
    {
        $target = array_reverse(explode('\\', get_class($subject)));

        return $target[0];
    }
}