<?php

namespace AppBundle\Menu;

class MenuAccount
{
    /**
     * @var menuMap
     */
    private static $menuMap = [
        'contacts' => [
            'name' => 'Contatos',
            'route' => 'contact_index',
            'context' => 'person',
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
                    'type' => 'module',
                    'icon' => 'modules',
                    'allowedRoles' => '*'
                ],
                'inverters' => [
                    'name' => 'Inversores',
                    'route' => 'components',
                    'type' => 'inverter',
                    'icon' => 'inverters',
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
            'icon' => 'sum',
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
                    'context' => 'contact_category',
                    'allowedRoles' => [
                        'owner'
                    ]
                ],
                'saleSteps' => [
                    'name' => 'Etapas de Venda',
                    'route' => 'categories',
                    'icon' => 'sale_stages',
                    'context' => 'sale_stage',
                    'allowedRoles' => [
                        'owner'
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
