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
            'route' => 'app_index',
            'icon' => 'dashboard',
            'allowedRoles' => '*'
        ],
        'contacts' => [
            'name' => 'Contatos',
            'route' => 'contact_index',
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
            'route' => 'task_index',
            'icon' => 'tasks',
            'allowedRoles' => '*'
        ],
        'projects' => [
            'name' => 'Projetos',
            'route' => 'project_index',
            'icon' => 'projects',
            'allowedRoles' => '*'
        ],
        'myItems' => [
            'name' => 'Meus Itens',
            'route' => 'extras_index',
            'icon' => 'extras',
            'allowedRoles' => [
                'ownerMaster',
                'owner'
            ]
        ],
        'sellPrice' => [
            'name' => 'Preço de Venda',
            'route' => 'kit_index',
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
                ]
            ]
        ],
        'weather' => [
            'name' => 'Dados Climáticos',
            'route' => 'nasa',
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
                'owner'
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
                        'owner'
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
                        'owner'
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
