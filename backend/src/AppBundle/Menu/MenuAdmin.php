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
            'route' => 'app_index',
            'icon' => 'dashboard',
            'allowedRoles' => '*'
        ],
        'accounts' => [
            'name' => 'Contas',
            'route' => 'account_index',
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
            'route' => 'ranking_index',
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
            'route' => 'memorials',
            'icon' => 'bars',
            'allowedRoles' => [
                'admin',
                'master'
            ]
        ],
        'orders' => [
            'name' => 'Orçamentos',
            'route' => 'orders',
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
                    'route' => 'components',
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
                    'route' => 'components',
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
                    'route' => 'structure_index',
                    'icon' => 'structure',
                    'allowedRoles' => '*'
                ],
                'stringBox' => [
                    'name' => 'String Box',
                    'route' => 'stringbox_index',
                    'icon' => 'stringbox',
                    'allowedRoles' => '*'
                ],
                'varieties' => [
                    'name' => 'Variedades',
                    'route' => 'variety_index',
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
        'stock' => [
            'name' => 'Estoque',
            'route' => 'stock',
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
