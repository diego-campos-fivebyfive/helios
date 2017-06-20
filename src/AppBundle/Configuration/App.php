<?php

namespace AppBundle\Configuration;

class App
{
    const BUNDLE = 'AppBundle';
    const LAYOUT = 'AppBundle::layout.html.twig';
    const MENU = 'AppBundle::menu.html.twig';
    const VIEW_EXTENSION = 'html.twig';
    const CUSTOMER_MANAGER = 'kolina_customer.manager';
    const APP_NAME = 'Inovador Solar';
    const APP_TITLE = 'Dashboard';
    const TRAIL_INTERVAL = '5 days';
    const PAYMENT_REJECTED_INTERVAL = '4 days';
    const BILLS_PENDING_AFTER = '10 days ago';

    /**
     * @param null $path
     * @return array|string
     */
    public static function icons($path = null)
    {
        $icons = [
            'accounts' => 'fa fa-bookmark',
            'admin_components' => 'fa fa-toggle-on',
            'business' => 'fa fa-building',
            'categories' => 'fa fa-list-ul',
            'company' => 'fa fa-building',
            'components' => 'fa fa-cube',
            'contacts' => 'fa fa-book',
            'dashboard' => 'fa fa-dashboard',
            'default' => 'fa fa-info',
            'globe' => 'fa fa-globe',
            'inverters' => 'fa fa-exchange',
            'kits' => 'fa fa-cubes',
            'modules' => 'fa fa-th',
            'money' => 'fa fa-money',
            'packages' => 'fa fa-archive',
            'person' => 'fa fa-user',
            'plug' => 'fa fa-plug',
            'profile' => 'fa fa-male',
            'projects' => 'fa fa-paste',
            'proposal' => 'fa fa-file-text-o',
            'published' => 'fa fa-check',
            'sale_stages' => 'fa fa-list-ol',
            'settings' => 'fa fa-cogs',
            'summary' => 'fa fa-list-alt',
            'sun' => 'fa fa-sun-o',
            'tasks' => 'fa fa-check-square',
            'unpublished' => 'fa fa-ban',
            'users' => 'fa fa-users',
            'signature' => 'fa fa-credit-card',
            'support' => 'fa fa-support'
        ];

        if($path){
            return array_key_exists($path, $icons) ? $icons[$path] : $icons['default'];
        }

        return $icons;
    }

    /**
     * @return array
     */
    public static function getRouteMapping()
    {
        return [
            'logout' => 'fos_user_security_logout'
        ];
    }

    /**
     * @return array
     */
    public static function getTopbarButtons($route)
    {
        $buttons = [
            'create' => [
                'theme' => [
                    'class' => 'btn btn-sm btn-outline btn-primary',
                    'icon' => 'fa-plus'
                ],
                'sources' => [
                    'account_index' => [
                        'route' => 'account_create',
                        'label' => 'Add',
                        'parameters' => ['context']
                    ],
                    'package_index' => [
                        'route' => 'package_create',
                        'label' => 'Add'
                    ],
                    /*'member_index' => [
                        'route' => 'member_create',
                        'label' => 'Add'
                    ],*/
                    'member_update' => [
                        'route' => 'member_create',
                        'label' => 'Add',
                        'parameters' => false
                    ],
                    /*'contact_index' => [
                        'route' => 'contact_create',
                        'label' => 'Add'
                    ],*/
                    'contact_index' => [
                        'targets' => [
                            [ 'route' => 'contact_create', 'label' => 'Adicionar Pessoa', 'theme' => ['icon' => 'fa-user'], 'parameters' => ['context' => 'person']],
                            [ 'route' => 'contact_create', 'label' => 'Adicionar Empresa', 'theme' => ['icon' => 'fa-building'], 'parameters' => ['context' => 'company']]
                        ]
                    ],
                    'team_index' => [
                        'route' => 'team_create',
                        'label' => 'Add'
                    ],
                    'team_update' => [
                        'route' => 'team_create',
                        'label' => 'Add',
                        'parameters' => false
                    ],
                    'team_manage' => [
                        'route' => 'team_create',
                        'label' => 'Add',
                        'parameters' => false
                    ],
                    'module_index' => [
                        'route' => 'module_create',
                        'label' => 'Add',
                        'parameters' => false
                    ],
                    'inverter_index' => [
                        'route' => 'inverter_create',
                        'label' => 'Add'
                    ],
                    'maker_index' => [
                        'route' => 'maker_create',
                        'label' => 'Add',
                        'parameters' => false
                    ],
                    'catalog_index' => [
                        'route' => 'catalog_select',
                        'label' => 'Add Platform Component'
                    ],
                    /*'kit_index' => [
                        'route' => 'kit_create',
                        'label' => 'Add'
                    ],*/
                    'project_index' => [
                        'route' => 'project_create',
                        'label' => 'Create Project'
                    ]
                ]
            ],
            'defaults' => [
                'theme' => [
                    'class' => 'btn btn-sm btn-default',
                    'icon' => 'fa-list'
                ],
                'sources' => [
                    'catalog_select' => [
                        'targets' => [
                            [ 'route' => 'catalog_select', 'label' => 'Show Modules', 'theme' => ['icon' => 'fa-cubes'], 'parameters' => ['type' => 'module']],
                            [ 'route' => 'catalog_select', 'label' => 'Show Inverters', 'theme' => ['icon' => 'fa-sliders'], 'parameters' => ['type' => 'inverter']]
                        ]
                    ]
                ]
            ]
        ];

        $result = [];
        foreach ($buttons as $action => $button)
            {
            if (array_key_exists($route, $button['sources']))
            {
                $result[$action]['theme'] = $buttons[$action]['theme'];
                $result[$action]['targets'][] = $button['sources'][$route];
            }
            }

        return $result;
    }

}
