<?php

namespace AppBundle\Menu;

use AppBundle\Configuration\App;
use AppBundle\Entity\UserInterface;

class Skeleton extends AbstractMenu
{
    private function getIcon($icon)
    {
        $class = str_replace('fa', '', App::icons($icon));
        return str_replace(' -', '', $class);
    }

    private function formatMenuItem($item)
    {
        $router = $this->container->get('router');
        dump($router); die;
        return [
            'name' => $item['name'],
            'link' => $item['route'],
            'icon' => $this->getIcon($item['icon'])
        ];
    }

    private function formatDropdown($item)
    {
        return [
            'name' => $item['name'],
            'icon' => $this->getIcon($item['icon']),
            'dropdown' => true
        ];
    }

    public function getMenuSkeleton()
    {
        $menuMap = $this->getMenuMap();
        $menu = [];

        foreach ($menuMap as $itemKey => $item) {
            if (!$this->userHasGroupAccess($item['allowedRoles'])) {
                continue;
            }

            if (!array_key_exists('subItems', $item)) {
                $menu[$itemKey] = $this->formatMenuItem($item);
                continue;
            }

            $menu[$itemKey] = $this->formatDropdown($item);

            $subItems = [];

            foreach ($item['subItems'] as $subItemKey => $subItem) {
                if ($this->userHasGroupAccess($subItem['allowedRoles'])) {
                    $subItems[$subItemKey] = $this->formatMenuItem($subItem);
                }
            }

            $menu[$itemKey]['subItems'] = $subItems;
        }

        return $menu;
    }
}
