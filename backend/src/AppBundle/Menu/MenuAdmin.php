<?php

namespace AppBundle\Menu;

use Knp\Menu\ItemInterface;
use AppBundle\Entity\UserInterface;

trait MenuAdmin
{
    /**
     * @var menuMap
     */
    private $menuMap;

    /**
     * MenuAdmin constructor
     */
    function __construct()
    {
        $this->menuMap = [
            'Accounts' => [
                'name' => 'Accounts',
                'route' => 'account_index',
                'icon' => 'accounts',
                'access_groups' => '*'
            ],
            'Ranking' => [
                'name' => 'Fidelidade SICES',
                'route' => 'ranking_index',
                'icon' => 'trophy',
                'access_groups' => '*'
            ],
            'Memorials' => [
                'name' => 'Memoriais',
                'route' => 'memorials',
                'icon' => 'bars',
                'access_groups' => [
                    'admin',
                    'master'
                ]
            ],
            'Orders' => [
                'name' => 'Orçamentos',
                'route' => 'orders',
                'icon' => 'orders',
                'access_groups' => '*'
            ],
            'Components' => [
                'name' => 'Componentes',
                'route' => 'stock',
                'icon' => 'beer',
                'access_groups' => [
                    'admin',
                    'commercial',
                    'expanse',
                    'master'
                ]
            ],
            'Stock' => [
                'name' => 'Estoque',
                'route' => 'stock',
                'icon' => 'kits',
                'access_groups' => [
                    'admin',
                    'commercial',
                    'expanse',
                    'master'
                ]
            ],
            'Users' => [
                'name' => 'Usuários Sices',
                'route' => 'user_index',
                'icon' => 'users',
                'access_groups' => [
                    'admin',
                    'master'
                ]
            ],
            'PaymentMethods' => [
                'name' => 'Cond. Pagamento',
                'route' => 'payment_methods',
                'icon' => 'signature',
                'access_groups' => [
                    'admin',
                    'master'
                ]
            ],
           'Insurance' => [
                'name' => 'Seguros',
                'route' => 'insurance_index',
                'icon' => 'insurance',
                'access_groups' => [
                    'admin',
                    'master'
                ]
            ]/*,
           'Settings' => [
                'name' => 'Settings',
                'uri' => '#',
                'class' => 'nav nav-second-level collapse',
                'icon' => 'settings',
                'access' => '*',
                'dropdown' => 'true',
                'items' => [
                    'My data' => [
                        'route' => 'member_profile',
                        'icon' => 'profile',
                    ],
                    'Parâmetros', [
                        'route' => 'platform_settings',
                        'icon' => 'sliders',
                    ]
                ]
            ]*/
        ];
    }

    /**
     * @param ItemInterface $menu
     */
    private function addMenuItem(ItemInterface $menu, $item)
    {
        $menu->addChild($item['name'], [
            'route' => $item['route'],
            'extras' => [
                'icon' => self::icon($item['icon'])
            ]
        ]);
    }

    private function hasGroupAccess($accessGroups, $userRoles)
    {
        $platformRoles = [
            'admin' => UserInterface::ROLE_PLATFORM_ADMIN,
            'after_sales' => UserInterface::ROLE_PLATFORM_AFTER_SALES,
            'commercial' => UserInterface::ROLE_PLATFORM_COMMERCIAL,
            'expanse' => UserInterface::ROLE_PLATFORM_EXPANSE,
            'financial' => UserInterface::ROLE_PLATFORM_FINANCIAL,
            'financing' => UserInterface::ROLE_PLATFORM_FINANCING,
            'master' => UserInterface::ROLE_PLATFORM_MASTER
        ];

        if ($accessGroups === '*') {
            return true;
        }

        foreach ($accessGroups as $groupName) {
            if (in_array($platformRoles[$groupName], $userRoles)) {
                return true;
            }
        }

        return false;
    }

    public function admin(ItemInterface $menu)
    {
        /** @var UserInterface $user */
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        foreach ($this->menuMap as $menuItem) {
            if(self::hasGroupAccess($menuItem['access_groups'], $userRoles)) {
                self::addMenuItem($menu, $menuItem);
            }
        }

        return $menu;
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
