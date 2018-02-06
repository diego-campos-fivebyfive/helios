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
    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function sidebar(FactoryInterface $factory, array $options)
    {
        $user = $this->getUser();

        $menu = $factory->createItem('root', [
            'childrenAttributes' => ['id' => 'side-menu', 'class' => 'nav metismenu']
        ]);

        $menu->addChild('Dashboard', [
            'route' => 'app_index',
            'extras' => ['icon' => App::icons('dashboard')]
        ]);

        if($user->isPlatform()) {

            // MENU EXCLUSIVO DA PLATAFORMA (Admin) - MenuAdmin
            $this->admin($menu);

            $this->resolveActiveMenu($menu);

            return $menu;
        }

	//$menuAccount = new MenuAccount();
        //$menuAccount->account($menu);
	$this->account($menu);


        if ($user->isSuperAdmin()) {
            $this->menuSuperAdmin($menu);
        }

        $this->resolveActiveMenu($menu);

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    private function menuPlatform(ItemInterface &$menu)
    {
        $platform = $menu->addChild('Administração', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
            'extras' => ['icon' => 'fa fa-th-large']
        ]);

        $this->admin($platform);
    }

    /**
     * @param ItemInterface $menu
     * @param UserInterface $user
     */
    private function menuSuperAdmin($menu, $user)
    {
        if($user->isSuperAdmin()){

            $oauth = $menu->addChild('API Auth', [
                'uri' => '#',
                'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
                'extras' => ['icon' => 'fa fa-refresh']
            ]);

            $oauth->addChild('Clients', [
                'route' => 'api_clients',
                'extras' => ['icon' => App::icons('users')]
            ]);
        }
    }

    private function addMenuItem($parent, $item)
    {
        $params = [
            'route' => $item['route'],
            'extras' => [
                'icon' => self::icon($item['icon'])
            ]
        ];

        if (array_key_exists('type', $item)) {
            $params['routeParameters'] = [
                'type' => $item['type']
	        ];
        }

        if (array_key_exists('context', $item)) {
            $params['routeParameters'] = [
                'context' => $item['context']
	        ];
        }

        if (array_key_exists('id', $item)) {
            $params['attributes'] = [
                'id' => 'idPedidos'
	        ];
        }

        $parent->addChild($item['name'], $params);
    }

    private function addDropdownItem($parent, $item)
    {
        return $parent->addChild($item['name'], [
            'uri' => $item['uri'],
            'childrenAttributes' => [
                'class' => 'nav nav-second-level collapse'
            ],
            'extras' => [
                'icon' => self::icon($item['icon'])
            ]
        ]);
    }

    private function hasGroupAccess($allowedRoles, $userRoles, $groupRoles)
    {
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

    public function account(ItemInterface $menu)
    {
        /** @var User $user */
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        if ($user->isPlatform()) {
            $groupRoles = User::getPlatformGroupRoles();
        } else {
            $groupRoles = User::getAccountGroupRoles();
        }

        foreach (MenuAccount::getMenuMap() as $item) {
            if (!self::hasGroupAccess(
                $item['allowedRoles'],
                $userRoles,
                $groupRoles
            )) {
                continue;
            }

            if (!array_key_exists('subItems', $item)) {
                self::addMenuItem($menu, $item);
                continue;
            }

            $dropdown = self::addDropdownItem($menu, $item);

            foreach($item['subItems'] as $subItem) {
                if (self::hasGroupAccess(
                    $subItem['allowedRoles'],
                    $userRoles,
                    $groupRoles
                )) {
                    self::addMenuItem($dropdown, $subItem);
                }
            }
        }

        return $menu;
    }
}
