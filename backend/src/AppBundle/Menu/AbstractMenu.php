<?php

namespace AppBundle\Menu;

use AppBundle\Entity\User;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\Business\TermsChecker;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class AbstractMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var UserInterface $user
     */
    protected $user;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @return UserInterface
     */
    protected function getUser()
    {
        if (!$this->user instanceof UserInterface) {
            $tokenStorage = $this->container->get('security.token_storage');

            /** @var UserInterface user */
            $this->user = $tokenStorage->getToken()->getUser();
        }

        return $this->user;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function getCurrentPathRequest()
    {
        if (!$this->request) {
            $requestStack = $this->container->get('request_stack');
            $currentRequest = $requestStack->getCurrentRequest();
            $this->request = $currentRequest->getPathInfo();
        }

        return $this->request;
    }

    protected function userHasGroupAccess($allowedRoles)
    {
        /** @var User $user */
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        if ($user->isPlatform()) {
            $groupRoles = User::getPlatformGroupRoles();
        } else {
            $groupRoles = User::getAccountGroupRoles();
        }

        if ($allowedRoles === '*') {
            return true;
        }

        foreach ($allowedRoles as $rolesName) {
            if (in_array($groupRoles[$rolesName], $userRoles)) {
                return true;
            }
        }

        return false;
    }

    protected function getMenuMap()
    {
        /** @var TermsChecker $termsChecker */
        $termsChecker = $this->container->get('terms_checker');

        $accountTerms = $this->getUser()->getInfo()->getAccount()->getTerms();

        $uncheckedTerms = $termsChecker->synchronize($accountTerms)->unchecked();

        /** @var User $user */
        $user = $this->getUser();

        if ($user->isPlatform()) {
            return MenuAdmin::getMenuMap();
        } else {
            if (empty($uncheckedTerms)) {
                return MenuAccount::getMenuMap();
            } else {
                return MenuAccountOnlyTerms::getMenuMap();
            }
        }
    }
}
