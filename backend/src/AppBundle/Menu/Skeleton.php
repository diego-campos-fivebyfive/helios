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

    private function getRouteParameters($item)
    {
        if (!isset($item['custom'])) {
            return [];
        }

        if (!isset($item['custom']['routeParameters'])) {
            return [];
        }

        return $item['custom']['routeParameters'];
    }

    private function getMenuItemLink($item)
    {
        if (isset($item['link'])) {
            return $item['link'];
        }

        $router = $this->container->get('router');

        return $router->generate(
            $item['route'],
            $this->getRouteParameters($item));
    }

    private function formatMenuItem($item)
    {
        return [
            'name' => $item['name'],
            'link' => $this->getMenuItemLink($item),
            'icon' => $this->getIcon($item['icon']),
            'customStyle' => $item['customStyle'] ?? null
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

            if (!isset($item['subItems'])) {
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
