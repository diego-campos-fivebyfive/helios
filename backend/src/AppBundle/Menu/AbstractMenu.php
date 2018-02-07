<?php

namespace AppBundle\Menu;

use AppBundle\Entity\UserInterface;
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
            /** var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage */
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
            /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
            $requestStack = $this->container->get('request_stack');
            $currentRequest = $requestStack->getCurrentRequest();
            $this->request = $currentRequest->getPathInfo();
        }

        return $this->request;
    }
}
