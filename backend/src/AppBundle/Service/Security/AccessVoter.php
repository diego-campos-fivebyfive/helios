<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace AppBundle\Service\Security;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class AccessVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @param AccessDecisionManagerInterface $decisionManager
     */
    function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

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
            return $this->$method($subject, $token);
        }

        return false;
    }

    /**
     * @param Project $project
     * @param TokenInterface $token
     * @return bool
     */
    private function voteEditProject(Project $project, TokenInterface $token)
    {
        $user = $token->getUser();

        /** @var MemberInterface $member */
        $member = $user->getInfo();
        $projectMember = $project->getMember();

        if($projectMember != $member){

            $account = $member->getAccount();
            $projectAccount = $projectMember->getAccount();

            if($projectAccount == $account){
                return $member->isOwner() || $member->isMasterOwner();
            }

            return false;
        }

        return true;
    }

    /**
     * @param Customer $customer
     * @param TokenInterface $token
     * @return bool
     */
    private function voteEditCustomer(Customer $customer, TokenInterface $token)
    {
        $user = $token->getUser();
        $member = $user->getInfo();

        if ($member instanceof MemberInterface){

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
    private function voteEditCategory(CategoryInterface $category, TokenInterface $token)
    {
        $user = $token->getUser();
        /** @var MemberInterface $member */
        $member = $user->getInfo();
        $account = $member->getAccount();

        if($account->getId() === $category->getAccount()->getId()){
            return true;
        }

        return false;
    }

    /**
     * @param Order $order
     * @param TokenInterface $token
     * @return bool
     */
    private function voteEditOrder(Order $order, TokenInterface $token)
    {
        $user = $token->getUser();

        if($user->isPlatform())
            return true;

        $account = $order->getAccount();
        if(!$account instanceof AccountInterface)
            return false;

        /** @var MemberInterface $member */
        $member = $user->getInfo();

        return ($account === $member->getAccount());
    }

    /**
     * @param $object
     * @return bool
     */
    private function isBearableInstance($object)
    {
        return in_array(get_class($object), [
            Customer::class,
            Category::class,
            Project::class,
            Order::class
        ]);
    }

    /**
     * @param $subject
     * @param $attribute
     * @return string
     */
    private function determineMethod($subject, $attribute)
    {
        $blocks = array_reverse(explode('\\', get_class($subject)));

        return sprintf('vote%s%s', ucfirst($attribute), $blocks[0]);
    }
}
