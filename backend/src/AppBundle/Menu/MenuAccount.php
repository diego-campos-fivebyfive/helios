<?php

namespace AppBundle\Menu;

class MenuAccount
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
        'contacts' => [
            'name' => 'Contatos',
            'link' => '/contact/person',
            'custom' => [
                'routeParameters' => [
                    'context' => 'person'
                ]
            ],
             'icon' => 'contacts',
            'allowedRoles' => '*'
        ],
        'tasks' => [
            'name' => 'Tarefas',
            'link' => '/tasks/m',
            'icon' => 'tasks',
            'allowedRoles' => '*'
        ],
        'projects' => [
            'name' => 'Projetos',
            'link' => '/project',
            'icon' => 'projects',
            'allowedRoles' => '*'
        ],
        'myItems' => [
            'name' => 'Meus Itens',
            'link' => '/item',
            'icon' => 'extras',
            'allowedRoles' => [
                'ownerMaster',
                'owner'
            ]
        ],
        'sellPrice' => [
            'name' => 'Preço de Venda',
            'link' => '/price',
            'icon' => 'money',
            'allowedRoles' => [
                'ownerMaster',
                'owner'
            ]
        ],
        'components' => [
            'name' => 'Componentes',
            'uri' => '#',
            'icon' => 'components',
            'allowedRoles' => '*',
            'subItems' => [
                'modules' => [
                    'name' => 'Módulos',
                    'link' => '/components/module/',
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
                    'link' => '/components/inverter/',
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
                ]
            ]
        ],
        'weather' => [
            'name' => 'Dados Climáticos',
            'link' => '/settings/nasa',
            'icon' => 'sun',
            'allowedRoles' => [
                'ownerMaster',
                'owner'
            ]
        ],
        'users' => [
            'name' => 'Usuários',
            'route' => 'member_index',
            'icon' => 'users',
            'allowedRoles' => [
                'ownerMaster',
                'owner'
            ]
        ],
        'ranking' => [
            'name' => 'Fidelidade SICES',
            'route' => 'ranking_index',
            'icon' => 'trophy',
            'allowedRoles' => [
                'ownerMaster',
                'owner'
            ]
        ],
        'kits' => [
            'name' => 'Kits Fixos',
            'route' => 'index_kit',
            'icon' => 'cart-plus',
            'allowedRoles' => [
                'owner'
            ]
        ],
        'order' => [
            'name' => 'Orçamento SICES',
            'route' => 'index_order',
            'id' => 'idPedidos',
            'icon' => 'my-requests',
            'custom' => [
                'attributes' => [
                    'id' => 'idPedidos'
                ]
            ],
            'allowedRoles' => [
                'ownerMaster',
                'owner'
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
            'link' => '/terms',
            'icon' => 'file-text-o',
            'allowedRoles' => [
                'ownerMaster'
            ]
        ],
        'settings' => [
            'name' => 'Configurações',
            'uri' => '#',
            'icon' => 'settings',
            'allowedRoles' => '*',
            'subItems' => [
                'timezone' => [
                    'name' => 'Fuso Horário',
                    'route' => 'member_timezone',
                    'icon' => 'globe',
                    'custom' => [
                        'routeParameters' => [
                            'type' => 'module'
                        ]
                    ],
                    'allowedRoles' => '*'
                ],
                'myData' => [
                    'name' => 'Meus Dados',
                    'route' => 'member_profile',
                    'icon' => 'profile',
                    'allowedRoles' => '*'
                ],
                'myBusiness' => [
                    'name' => 'Meu Negócio',
                    'route' => 'member_business',
                    'icon' => 'business',
                    'allowedRoles' => [
                        'ownerMaster'
                    ]
                ],
                'categories' => [
                    'name' => 'Categorias',
                    'route' => 'categories',
                    'icon' => 'categories',
                    'custom' => [
                        'routeParameters' => [
                            'context' => 'contact_category'
                        ]
                    ],
                    'allowedRoles' => [
                        'owner', 'ownerMaster'
                    ]
                ],
                'saleSteps' => [
                    'name' => 'Etapas de Venda',
                    'route' => 'categories',
                    'icon' => 'sale_stages',
                    'custom' => [
                        'routeParameters' => [
                            'context' => 'sale_stage'
                        ]
                    ],
                    'allowedRoles' => [
                        'owner', 'ownerMaster'
                    ]
                ]
            ]
        ],
        'apiAuth' => [
            'name' => 'API Auth',
            'uri' => '#',
            'icon' => 'settings',
            'allowedRoles' => [
                'superAdmin'
            ],
            'subItems' => [
                'clients' => [
                    'name' => 'Clientes',
                    'route' => 'api_clients',
                    'icon' => 'users',
                    'allowedRoles' => [
                        'superAdmin'
                    ]
                ]
            ]
        ]
    ];

    public static function getMenuMap()
    {
        return self::$menuMap;
    }
}
