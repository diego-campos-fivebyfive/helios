<?php

namespace AppBundle\Menu;

use Knp\Menu\ItemInterface;
use AppBundle\Entity\UserInterface;

trait MenuAdmin
{
    public function admin(ItemInterface $menu)
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        $master = UserInterface::ROLE_PLATFORM_MASTER;
        $admin = UserInterface::ROLE_PLATFORM_ADMIN;
        $commercial = UserInterface::ROLE_PLATFORM_COMMERCIAL;

        $config = [
            'Accounts' => [$master, $admin, $commercial],
            'Memorials' => [$master, $admin],
            'Orders' => '*',
            'Components' => [$master, $admin, $commercial],
            'Stock' => [$master, $admin],
            'Users' => [$master, $admin],
            'PaymentMethods' => [$master, $admin],
            'Settings' => '*'
        ];

        $roles = $user->getRoles();

        foreach ($config as $item => $access){
            $method = 'add' . $item;

            if('*' === $access){
                self::$method($menu);
                continue;
            }
            foreach ($access as $role){
                if(in_array($role, $roles)){
                    $this->$method($menu);
                }
            }
        }

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     */
    private function addAccounts(ItemInterface $menu)
    {
        $menu->addChild('Accounts', [
            'route' => 'account_index',
            'extras' => ['icon' => self::icon('accounts')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function addMemorials(ItemInterface $menu)
    {
        $menu->addChild('Memoriais', [
            'route' => 'memorials',
            'extras' => ['icon' => self::icon('bars')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function addOrders(ItemInterface $menu)
    {
        $menu->addChild('Orçamentos', [
            'route' => 'orders',
            'extras' => ['icon' => self::icon('orders')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function addStock(ItemInterface $menu)
    {
        $menu->addChild('Estoque', [
            'route' => 'stock',
            'extras' => ['icon' => self::icon('kits')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function addUsers(ItemInterface $menu)
    {
        $menu->addChild('Usuários Sices', [
            'route' => 'user_index',
            'extras' => ['icon' => self::icon('users')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function addPaymentMethods(ItemInterface $menu)
    {
        $menu->addChild('Cond. Pagamento', [
            'route' => 'payment_methods',
            'extras' => ['icon' => self::icon('signature')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function addSettings(ItemInterface $menu)
    {
        $settings = $menu->addChild('Settings', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
            'extras' => ['icon' => self::icon('settings')]
        ]);

        $settings->addChild('My data', [
            'route' => 'member_profile',
            'extras' => ['icon' => self::icon('profile')]
        ]);

        if($this->user->isPlatformMaster() || $this->user->isPlatformAdmin()){
            $settings->addChild('Parâmetros', [
                'route' => 'platform_settings',
                'extras' => ['icon' => self::icon('sliders')]
            ]);
        }
    }
}
