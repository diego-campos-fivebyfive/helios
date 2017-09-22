<?php

namespace AppBundle\Menu;

use Knp\Menu\ItemInterface;

trait MenuAdmin
{
    public function admin(ItemInterface $menu)
    {
        $user = $this->getUser();

        /**
         * Remover os ifs que utilizam esta variável e excluí-la após
         * a estabilização das roles
         */
        $isAdmin = $user->isAdmin();

        $menu->addChild('Accounts', [
            'route' => 'account_index',
            'extras' => ['icon' => self::icon('accounts')]
        ]);

        $menu->addChild('Memoriais', [
            'route' => 'memorials',
            'extras' => ['icon' => self::icon('bars')]
        ]);

        $menu->addChild('Orçamentos', [
            'uri' => '#',
            'extras' => ['icon' => self::icon('bars')]
        ]);

        $menu->addChild('Lista de Sistemas', [
            'uri' => '#',
            'extras' => ['icon' => self::icon('th')]
        ]);

        $menu->addChild('Usuários Sices', [
            'route' => 'user_index',
            'extras' => ['icon' => self::icon('users')]
        ]);

        if(!$isAdmin)
        $this->addComponents($menu);

        if(!$isAdmin) {

            $settings = $menu->addChild('Settings', [
                'uri' => '#',
                'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
                'extras' => ['icon' => self::icon('settings')]
            ]);

            $settings->addChild('My data', [
                'route' => 'member_profile',
                'extras' => ['icon' => self::icon('profile')]
            ]);
        }

        return $menu;
    }
}
