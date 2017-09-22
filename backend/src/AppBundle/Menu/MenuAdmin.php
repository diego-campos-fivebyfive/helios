<?php

namespace AppBundle\Menu;

use AppBundle\Configuration\App;
use Knp\Menu\FactoryInterface;

class Admin extends AbstractMenu
{
    public function sidebar(FactoryInterface $factory, array $options)
    {
        $user = $this->getUser();

        $menu = $factory->createItem('root', [
            'childrenAttributes' => ['id' => 'side-menu', 'class' => 'nav metismenu']
        ]);

        $menu->addChild('Accounts', [
            'route' => 'account_index',
            'extras' => ['icon' => App::icons('accounts')]
        ]);

        $menu->addChild('Memoriais', [
            'route' => 'memorials',
            'extras' => ['icon' => 'fa fa-bars']
        ]);

        $menu->addChild('Users', [
            'route' => 'user_index',
            'extras' => ['icon' => App::icons('users')]
        ]);

        return $menu;
    }
}
