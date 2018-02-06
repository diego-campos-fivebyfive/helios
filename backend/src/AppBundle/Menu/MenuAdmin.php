<?php

namespace AppBundle\Menu;

use Knp\Menu\ItemInterface;
use AppBundle\Entity\User;

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
            'accounts' => [
                'name' => 'Accounts',
                'route' => 'account_index',
                'icon' => 'accounts',
                'allowedGroups' => [
                    'admin',
                    'afterSales',
                    'commercial',
                    'expanse',
                    'financial',
                    'financing',
                    'master'
                ]
            ],
            'ranking' => [
                'name' => 'Fidelidade SICES',
                'route' => 'ranking_index',
                'icon' => 'trophy',
                'allowedGroups' => '*'
            ],
            'memorials' => [
                'name' => 'Memoriais',
                'route' => 'memorials',
                'icon' => 'bars',
                'allowedGroups' => [
                    'admin',
                    'master'
                ]
            ],
            'orders' => [
                'name' => 'Orçamentos',
                'route' => 'orders',
                'icon' => 'orders',
                'allowedGroups' => '*'
            ],
            'components' => [
                'name' => 'Componentes',
                'uri' => '#',
                'icon' => 'components',
                'allowedGroups' => [
                    'admin',
                    'commercial',
                    'expanse',
                    'master'
                ],
                'subItems' => [
                    'modules' => [
                        'name' => 'Módulos',
                        'route' => 'components',
                        'type' => 'module',
                        'icon' => 'modules',
                        'allowedGroups' => '*'
                    ],
                    'inverters' => [
                        'name' => 'Inversores',
                        'route' => 'components',
                        'type' => 'inverter',
                        'icon' => 'inverters',
                        'allowedGroups' => '*'
                    ],
                    'structures' => [
                        'name' => 'Estruturas',
                        'route' => 'structure_index',
                        'icon' => 'structure',
                        'allowedGroups' => '*'
                    ],
                    'stringBox' => [
                        'name' => 'String Box',
                        'route' => 'stringbox_index',
                        'icon' => 'stringbox',
                        'allowedGroups' => '*'
                    ],
                    'varieties' => [
                        'name' => 'Variedades',
                        'route' => 'variety_index',
                        'icon' => 'variety',
                        'allowedGroups' => '*'
                    ]
                ]
            ],
            'stock' => [
                'name' => 'Estoque',
                'route' => 'stock',
                'icon' => 'kits',
                'allowedGroups' => [
                    'admin',
                    'commercial',
                    'expanse',
                    'master'
                ]
            ],
            'users' => [
                'name' => 'Usuários Sices',
                'route' => 'user_index',
                'icon' => 'users',
                'allowedGroups' => [
                    'admin',
                    'master'
                ]
            ],
            'paymentMethods' => [
                'name' => 'Cond. Pagamento',
                'route' => 'payment_methods',
                'icon' => 'signature',
                'allowedGroups' => [
                    'admin',
                    'master'
                ]
            ],
           'insurance' => [
                'name' => 'Seguros',
                'route' => 'insurance_index',
                'icon' => 'insurance',
                'allowedGroups' => [
                    'admin',
                    'master'
                ]
            ],
           'settings' => [
                'name' => 'Settings',
                'uri' => '#',
                'icon' => 'settings',
                'allowedGroups' => '*',
                'subItems' => [
                    'myData' => [
                        'name' => 'Meus Dados',
                        'route' => 'member_profile',
                        'icon' => 'profile',
                        'allowedGroups' => '*'
                    ],
                    'parameters' => [
                        'name' => 'Parâmetros',
                        'route' => 'platform_settings',
                        'icon' => 'sliders',
                        'allowedGroups' => [
                            'admin',
                            'master'
                        ]
                    ]
                ]
            ]
        ];
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

    private function hasGroupAccess($allowedGroups, $userRoles)
    {
        $platformRoles = User::getPlatformGroupRoles();

        if ($allowedGroups === '*') {
            return true;
        }

        foreach ($allowedGroups as $groupName) {
            if (in_array($platformRoles[$groupName], $userRoles)) {
                return true;
            }
        }

        return false;
    }

    public function admin(ItemInterface $menu)
    {
        /** @var User $user */
        $user = $this->getUser();

        $userRoles = $user->getRoles();

        foreach ($this->menuMap as $item) {
            if (!self::hasGroupAccess($item['allowedGroups'], $userRoles)) {
                continue;
            }

            if (!array_key_exists('subItems', $item)) {
                self::addMenuItem($menu, $item);
                continue;
            }

            $dropdown = self::addDropdownItem($menu, $item);

            foreach($item['subItems'] as $subItem) {
                if (self::hasGroupAccess($subItem['allowedGroups'], $userRoles)) {
                    self::addMenuItem($dropdown, $subItem);
                }
            }
        }

        return $menu;
    }
}
