<?php

namespace AppBundle\Menu;

use Knp\Menu\ItemInterface;
use AppBundle\Configuration\App;
use AppBundle\Entity\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class AbstractMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;
//    use MenuAccount;
//    use MenuAdmin;

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
        if(!$this->user instanceof UserInterface) {
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
    private function getRequest()
    {
        if(!$this->request){
            /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
            $requestStack = $this->container->get('request_stack');
            $this->request = $requestStack->getCurrentRequest();
        }

        return $this->request;
    }

    /**
     * @param ItemInterface $menu
     */
    protected function setActiveMenu(ItemInterface &$menu)
    {
        if ($menu->hasChildren()) {
            foreach ($menu->getChildren() as $child) {
                $this->setActiveMenu($child);
            }
        }

        $uri = $this->getRequest()->getPathInfo();

        if ($uri !== $menu->getUri()) {
            return;
        }

        $menu->setAttribute('class', 'active');

        if ($menu->getParent() && !$menu->getParent()->isRoot()) {
            $menu->getParent()->setAttribute('class', 'active');
        }
   }

    /**
     * @param $icon
     * @return array|string
     */
    protected static function icon($icon)
    {
        return App::icons($icon);
    }
}
