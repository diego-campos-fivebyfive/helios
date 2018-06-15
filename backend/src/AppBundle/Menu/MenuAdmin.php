<?php

namespace AppBundle\Menu;

class MenuAdmin
{
    /**
     * @var menuMap
     */
    private static $menuMap = [
        'dashboard' => [
            'name' => 'Dashboard',
            'link' => '/',
            'icon' => 'dashboard',
            'allowedRoles' => '*'
        ],
        'accounts' => [
            'name' => 'Contas',
            'link' => '/admin/account',
            'icon' => 'accounts',
            'allowedRoles' => [
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
            'link' => '/ranking',
            'icon' => 'trophy',
            'allowedRoles' => '*'
        ],
        'coupon' => [
            'name' => 'Cupom de Desconto',
            'link' => '/coupon',
            'icon' => 'ticket',
            'allowedRoles' => [
              'admin',
              'master'
            ]
        ],
        'memorials' => [
            'name' => 'Memoriais',
            'link' => '/admin/memorials',
            'icon' => 'bars',
            'allowedRoles' => [
                'admin',
                'master'
            ]
        ],
        'orders' => [
            'name' => 'Orçamentos',
            'link' => '/admin/orders',
            'icon' => 'orders',
            'allowedRoles' => '*'
        ],
        'components' => [
            'name' => 'Componentes',
            'uri' => '#',
            'icon' => 'components',
            'allowedRoles' => [
                'admin',
                'commercial',
                'expanse',
                'master'
            ],
            'subItems' => [
                'modules' => [
                    'name' => 'Módulos',
                    'link' => '/components/module',
                    'icon' => 'modules',
                    'custom' => [
                        'routeParameters' => [
                            'type' => 'module'
                        ]
                    ],
                    'allowedRoles' => '*'
                ],
                'inverters' => [
                    'name' => 'Inversores',
                    'link' => '/components/inverter',
                    'icon' => 'inverters',
                    'custom' => [
                        'routeParameters' => [
                            'type' => 'inverter'
                        ]
                    ],
                    'allowedRoles' => '*'
                ],
                'structures' => [
                    'name' => 'Estruturas',
                    'link' => '/structure',
                    'icon' => 'structure',
                    'allowedRoles' => '*'
                ],
                'stringBox' => [
                    'name' => 'String Box',
                    'link' => '/stringbox',
                    'icon' => 'stringbox',
                    'allowedRoles' => '*'
                ],
                'varieties' => [
                    'name' => 'Variedades',
                    'link' => '/variety',
                    'icon' => 'variety',
                    'allowedRoles' => '*'
                ],
                'makers' => [
                    'name' => 'Fabricantes',
                    'route' => 'maker_index',
                    'icon' => 'building',
                    'allowedRoles' => [
                        'master'
                    ]
                ]
            ]
        ],
        'kits' => [
            'name' => 'Kits Fixos',
            'link' => '/admin/kit',
            'icon' => 'list',
            'allowedRoles' => [
                'master',
                'admin'
            ]
        ],
        'stock' => [
            'name' => 'Estoque',
            'link' => '/admin/stock',
            'icon' => 'kits',
            'allowedRoles' => [
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
            'allowedRoles' => [
                'admin',
                'master'
            ]
        ],
        'paymentMethods' => [
            'name' => 'Cond. Pagamento',
            'route' => 'payment_methods',
            'icon' => 'signature',
            'allowedRoles' => [
                'admin',
                'master'
            ]
        ],
       'insurance' => [
            'name' => 'Seguros',
            'route' => 'insurance_index',
            'icon' => 'insurance',
            'allowedRoles' => [
                'admin',
                'master'
            ]
        ],
        'utils' => [
            'name' => 'Links Úteis',
            'link' => 'https://suporte.plataformasicessolar.com.br/faq/links-uteis',
            'icon' => 'link',
            'custom' => [
                'attributes' => [
                    'id' => 'idUtils'
                ]
            ],
            'allowedRoles' => '*',
        ],
        'terms' => [
            'name' => 'Termos de Uso',
            'link' => '/terms-of-use',
            'icon' => 'file-text-o',
            'allowedRoles' => [
                'master'
            ]
        ],
       'settings' => [
            'name' => 'Configurações',
            'uri' => '#',
            'icon' => 'settings',
            'allowedRoles' => '*',
            'subItems' => [
                'myData' => [
                    'name' => 'Meus Dados',
                    'route' => 'member_profile',
                    'icon' => 'profile',
                    'allowedRoles' => '*'
                ],
                'parameters' => [
                    'name' => 'Parâmetros',
                    'route' => 'platform_settings',
                    'icon' => 'sliders',
                    'allowedRoles' => [
                        'admin',
                        'master'
                    ]
                ]
            ]
        ],
        'metrics' => [
            'name' => 'Métricas',
            'uri' => '#',
            'icon' => 'area-chart',
            'allowedRoles' => [
                'master'
            ],
            'subItems' => [
                'product' => [
                    'name' => 'Técnicas',
                    'link' => '/metrics',
                    'icon' => 'coffee',
                    'allowedRoles' => '*'
                ]
            ]
        ]
    ];

    public static function getMenuMap()
    {
        return self::$menuMap;
    }
}
