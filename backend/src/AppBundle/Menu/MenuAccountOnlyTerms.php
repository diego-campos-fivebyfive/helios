<?php

namespace AppBundle\Menu;

class MenuAccountOnlyTerms
{
    /**
     * @var menuMap
     */
    private static $menuMap = [
        'terms' => [
            'name' => 'Termos de Uso',
            'link' => '/terms',
            'icon' => 'file-text-o',
            'allowedRoles' => [
                'ownerMaster'
            ]
        ]
    ];

    public static function getMenuMap()
    {
        return self::$menuMap;
    }
}
