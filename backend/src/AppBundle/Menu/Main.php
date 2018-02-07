<?php

namespace AppBundle\Menu;

use AppBundle\Configuration\App;
use AppBundle\Entity\User;
use AppBundle\Entity\UserInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Main extends AbstractMenu
{
    private function addMenuItem($parent, $item)
    {
        $params = [
            'route' => $item['route'],
            'extras' => [
                'icon' => self::icon($item['icon'])
            ]
        ];

        if (array_key_exists('custom', $item)) {
            $params = array_merge($params, $item['custom']);
        }

        $parent->addChild($item['name'], $params);
    }

    private function addDropdownItem($parent, $item)
    {
        $params = [
            'uri' => $item['uri'],
            'childrenAttributes' => [
                'class' => 'nav nav-second-level collapse'
            ],
            'extras' => [
                'icon' => self::icon($item['icon'])
            ]
        ];

        return $parent->addChild($item['name'], $params);
    }

    private function hasGroupAccess($allowedRoles)
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

    private function getMenuMap()
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->isPlatform()) {
            return MenuAdmin::getMenuMap();
        } else {
            return MenuAccount::getMenuMap();
        }
    }
 
    private function includeMenuItems(ItemInterface $menu)
    {
        $menuMap = $this->getMenuMap();

        foreach ($menuMap as $item) {
            if (!$this->hasGroupAccess($item['allowedRoles'])) {
                continue;
            }

            if (!array_key_exists('subItems', $item)) {
                $this->addMenuItem($menu, $item);
                continue;
            }

            $dropdown = $this->addDropdownItem($menu, $item);

            foreach ($item['subItems'] as $subItem) {
                if ($this->hasGroupAccess($subItem['allowedRoles'])) {
                    $this->addMenuItem($dropdown, $subItem);
                }
            }
        }

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @return ItemInterface
     */
    public function sidebar(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root', [
            'childrenAttributes' => [
                'id' => 'side-menu',
                'class' => 'nav metismenu'
            ]
        ]);

        $this->includeMenuItems($menu);

        $this->setActiveMenu($menu);

        return $menu;
    }
}
