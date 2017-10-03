<?php

namespace AppBundle\Menu;

use Knp\Menu\ItemInterface;

trait MenuAccount
{
    public function account(ItemInterface $menu)
    {
        $menu->addChild('Contacts', [
            'route' => 'contact_index',
            'routeParameters' => ['context' => 'person'],
            'extras' => ['icon' => self::icon('contacts')]
        ]);

        $menu->addChild('Tasks', [
            'route' => 'task_index',
            'extras' => ['icon' => self::icon('tasks')]
        ]);

        $menu->addChild('Projects', [
            'route' => 'project_index',
            'extras' => ['icon' => self::icon('projects')]
        ]);

        $user = $this->getUser();

        /**
         * ROLE_OWNER
         */
        if($user->isOwner() || $user->isOwnerMaster()){

            $menu->addChild('Meus Itens', [
                'route' => 'extras_index',
                'extras' => ['icon' => self::icon('extras')]
            ]);

            $menu->addChild('Preço de Venda', [
                'route' => 'kit_index',
                'extras' => ['icon' =>self::icon('money')]
            ]);
        }

        self::addComponents($menu);
        
        if($user->isOwner() || $user->isOwnerMaster()){

            $menu->addChild('Climatic data', [
                'route' => 'nasa',
                'extras' => ['icon' => self::icon('sun')]
            ]);

            $menu->addChild('Users', [
                'route' => 'member_index',
                'extras' => ['icon' => self::icon('users')]
            ]);
        }

        if ($user->isOwner()) {
            $this->requestsMenu($menu, $user);
        }

        $this->menuSettings($menu);

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    private static function addComponents(ItemInterface &$menu)
    {
        $components = $menu->addChild('Components', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
            'extras' => ['icon' =>self::icon('components')]
        ]);

        $components->addChild('Modules', [
            'route' => 'components',
            'routeParameters' => ['type' => 'module'],
            'extras' => ['icon' =>self::icon('modules')]
        ]);

        $components->addChild('Inverters', [
            'route' => 'components',
            'routeParameters' => ['type' => 'inverter'],
            'extras' => ['icon' =>self::icon('inverters')]
        ]);

        $components->addChild('Estruturas', [
            'route' => 'structure_index',
            'extras' => ['icon' =>self::icon('structure')]
        ]);

        $components->addChild('String Box', [
            'route' => 'stringbox_index',
            'extras' => ['icon' =>self::icon('stringbox')]
        ]);

        $components->addChild('Variedades', [
            'route' => 'variety_index',
            'extras' => ['icon' =>self::icon('variety')]
        ]);
    }

    /**
     * Configure menu and sub menus for settings
     *
     * @param ItemInterface $menu
     */
    private function menuSettings(ItemInterface &$menu)
    {
        $user = $this->getUser();

        $settings = $menu->addChild('Settings', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
            'extras' => ['icon' =>self::icon('settings')]
        ]);

        if($user->isAdmin()){

            $this->menuTimezone($settings);

            return;
        }

        $settings->addChild('My data', [
            'route' => 'member_profile',
            'extras' => ['icon' =>self::icon('profile')]
        ]);

        /**
         * Restricted only to owner master
         */
        if($user->isOwnerMaster()) {

            $settings->addChild('My business', [
                'route' => 'member_business',
                'extras' => ['icon' =>self::icon('business')]
            ]);
        }

        $this->menuTimezone($settings);

        /**
         * Restricted only to owners
         */
        if($user->isOwner()){

            $settings->addChild('Categories', [
                'route' => 'categories',
                'routeParameters' => ['context' => 'contact_category'],
                'extras' => ['icon' =>self::icon('categories')]
            ]);

            $settings->addChild('Sale Steps', [
                'route' => 'categories',
                'routeParameters' => ['context' => 'sale_stage'],
                'extras' => ['icon' =>self::icon('sale_stages')]
            ]);
        }
    }

    /**
     * @param ItemInterface $menu
     */
    private function menuTimezone(ItemInterface &$menu)
    {
        $menu->addChild('Timezone', [
            'route' => 'member_timezone',
            'extras' => ['icon' => self::icon('globe')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function requestsMenu(ItemInterface &$menu)
    {
        $requests = $menu->addChild('Pedidos', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse orders'],
            'attributes' => ['id' => 'idPedidos'],
            'extras' => ['icon' => self::icon('requests')]
        ]);

        $requests->addChild('Orçamento', [
            'route' => 'project_generator',
            'extras' => ['icon' => self::icon('money')]
        ]);

        $requests->addChild('Meus Pedidos', [
            'route' => 'index_order',
            'extras' => ['icon' =>self::icon('my-requests')]
        ]);
    }
}
