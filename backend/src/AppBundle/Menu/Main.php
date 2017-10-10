<?php

namespace AppBundle\Menu;

use AppBundle\Configuration\App;
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
}
