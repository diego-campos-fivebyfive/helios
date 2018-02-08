<?php

namespace AppBundle\Menu;

use AppBundle\Configuration\App;
use AppBundle\Entity\UserInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Main extends AbstractMenu
{
    private function setActiveMenu(&$menu)
    {
        if ($menu->hasChildren()) {
            foreach ($menu->getChildren() as $child) {
                $this->setActiveMenu($child);
            }
        }

        if ($this->getCurrentPathRequest() !== $menu->getUri()) {
            return;
        }

        $menu->setAttribute('class', 'active');

        if ($menu->getParent() && !$menu->getParent()->isRoot()) {
            $menu->getParent()->setAttribute('class', 'active');
        }
    }

    private function getMenuItemParams($item)
    {
        $params = [
            'route' => $item['route'],
            'extras' => [
                'icon' => App::icons($item['icon'])
            ]
        ];

        if (!array_key_exists('custom', $item)) {
            return $params;
        }

        return array_merge($params, $item['custom']);
    }

    private function getDropdownParams($item)
    {
        return [
            'uri' => $item['uri'],
            'childrenAttributes' => [
                'class' => 'nav nav-second-level collapse'
            ],
            'extras' => [
                'icon' => App::icons($item['icon'])
            ]
        ];
    }

    public function getMenu()
    {
        $menuMap = $this->getMenuMap();
        $menu = [];

        foreach ($menuMap as $item) {
            if (!$this->userHasGroupAccess($item['allowedRoles'])) {
                continue;
            }

            if (!array_key_exists('subItems', $item)) {
                $menu[] = $item;
                continue;
            }

            $menu[] = $item;

            foreach ($item['subItems'] as $subItem) {
                if ($this->userHasGroupAccess($subItem['allowedRoles'])) {
                    $menu[$item['name']][] = $subItem;
                }
            }
        }

        return $menu;
    }


    private function includeMenuItems(ItemInterface $menu)
    {
        $menuMap = $this->getMenuMap();

        foreach ($menuMap as $item) {
            if (!$this->userHasGroupAccess($item['allowedRoles'])) {
                continue;
            }

            if (!array_key_exists('subItems', $item)) {
                $menu->addChild(
                    $item['name'],
                    $this->getMenuItemParams($item));

                continue;
            }

            $dropdown = $menu->addChild(
                $item['name'],
                $this->getDropdownParams($item));

            foreach ($item['subItems'] as $subItem) {
                if ($this->userHasGroupAccess($subItem['allowedRoles'])) {
                    $dropdown->addChild(
                        $subItem['name'],
                        $this->getMenuItemParams($subItem));
                }
            }
        }

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @return ItemInterface
     */
    public function sidebar(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root', [
            'childrenAttributes' => [
                'id' => 'side-menu',
                'class' => 'nav metismenu'
            ]
        ]);

        $this->includeMenuItems($menu);

        $this->setActiveMenu($menu);

        return $menu;
    }
}
