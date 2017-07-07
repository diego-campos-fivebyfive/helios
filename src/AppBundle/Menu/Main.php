<?php

namespace AppBundle\Menu;

use AppBundle\Configuration\App;
use AppBundle\Entity\UserInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Main implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function sidebar(FactoryInterface $factory, array $options)
    {
        $user = $this->getUser();
        $account = $user->getInfo()->getAccount();

        $menu = $factory->createItem('root', [
            'childrenAttributes' => ['id' => 'side-menu', 'class' => 'nav metismenu']
        ]);

        $menu->addChild('Dashboard', [
            'route' => 'app_index',
            'extras' => ['icon' => App::icons('dashboard')]
        ]);

        /**
         * ROLE_ADMIN
         */
        if($user->isAdmin()) {

            $menu->addChild('Accounts', [
                'route' => 'account_index',
                'extras' => ['icon' => App::icons('accounts')]
            ]);

            $menu->addChild('Packages', [
                'route' => 'package_index',
                'extras' => ['icon' => App::icons('packages')]
            ]);

            $this->menuAdminComponents($menu, $user);

            $this->menuSettings($menu, $user);

            $this->resolveActiveMenu($menu);

            return $menu;
        }

        $menu->addChild('Contacts', [
            'route' => 'contact_index',
            'routeParameters' => ['context' => 'person'],
            'extras' => ['icon' => App::icons('contacts')]
        ]);

        $menu->addChild('Tasks', [
            'route' => 'task_index',
            'extras' => ['icon' => App::icons('tasks')]
        ]);

        $menu->addChild('Projects', [
            'route' => 'project_index',
            'extras' => ['icon' => App::icons('projects')]
        ]);

        /**
         * ROLE_OWNER
         */
        if($user->isOwner()){

            $menu->addChild('Kits', [
                'route' => 'kit_index',
                'extras' => ['icon' => App::icons('kits')]
            ]);

            $this->addComponents($menu, $user);

            $menu->addChild('Climatic data', [
                'route' => 'nasa',
                'extras' => ['icon' => App::icons('sun')]
            ]);

            $menu->addChild('Users', [
                'route' => 'member_index',
                'extras' => ['icon' => App::icons('users')]
            ]);
        }

        $this->menuSettings($menu, $user);

        $this->menuSupport($menu);

        if($user->isOwnerMaster() && $account->isFreeAccount()) {

            $menu->addChild('Subscribe', [
                'route' => 'signature',
                'extras' => ['icon' => App::icons('signature')],
                'attributes' => [
                    'class' => 'special_link'
                ]
            ]);
        }

        $this->resolveActiveMenu($menu);

        return $menu;
    }

    /**
     * @param ItemInterface $menu
     * @param $user
     */
    private function menuAdminComponents(ItemInterface &$menu, $user)
    {
        $admin = $menu->addChild('Admin Components', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
            'extras' => ['icon' => App::icons('admin_components')]
        ]);

        $admin->addChild('Published', [
            'route' => 'components_published',
            'routeParameters' => ['context' => 'inverter'],
            'extras' => ['icon' => App::icons('published')]
        ]);

        $admin->addChild('Not published', [
            'route' => 'components',
            'extras' => ['icon' => App::icons('unpublished')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     * @param UserInterface $user
     */
    private function addComponents(ItemInterface &$menu, UserInterface $user)
    {
        $components = $menu->addChild('Components', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
            'extras' => ['icon' => App::icons('components')]
        ]);

        $components->addChild('Modules', [
            'route' => 'module_index',
            'extras' => ['icon' => App::icons('modules')]
        ]);

        $components->addChild('Inverters', [
            'route' => 'inverter_index',
            'extras' => ['icon' => App::icons('inverters')]
        ]);

        $components->addChild('Structures', [
            'route' => 'structure_index',
            'extras' => ['icon' => App::icons('structures')]
        ]);
    }

    /**
     * Configure menu and sub menus for settings
     *
     * @param ItemInterface $menu
     * @param UserInterface $user
     */
    private function menuSettings(ItemInterface &$menu, UserInterface $user)
    {
        $settings = $menu->addChild('Settings', [
            'uri' => '#',
            'childrenAttributes' => ['class' => 'nav nav-second-level collapse'],
            'extras' => ['icon' => App::icons('settings')]
        ]);

        if($user->isAdmin()){

            $this->menuTimezone($settings);

            return;
        }

        $settings->addChild('My data', [
            'route' => 'member_profile',
            'extras' => ['icon' => App::icons('profile')]
        ]);

        /**
         * Restricted only to owner master
         */
        if($user->isOwnerMaster()) {

            $settings->addChild('My business', [
                'route' => 'member_business',
                'extras' => ['icon' => App::icons('business')]
            ]);

            $settings->addChild('My signature', [
                'route' => 'signature',
                'extras' => ['icon' => App::icons('signature')]
            ]);
        }

        $this->menuTimezone($settings);

        /**
         * Restricted only to owners
         */
        if($user->isOwner()){

            $settings->addChild('Proposal', [
                'route' => 'document_configure',
                'extras' => ['icon' => App::icons('proposal')]
            ]);

            $settings->addChild('Categories', [
                'route' => 'categories',
                'routeParameters' => ['context' => 'contact_category'],
                'extras' => ['icon' => App::icons('categories')]
            ]);

            $settings->addChild('Sale Steps', [
                'route' => 'categories',
                'routeParameters' => ['context' => 'sale_stage'],
                'extras' => ['icon' => App::icons('sale_stages')]
            ]);
        }
    }

    /**
     * @param ItemInterface $menu
     */
    private function menuTimezone(ItemInterface &$menu)
    {
        $menu->addChild('Timezone', [
            'route' => 'member_timezone',
            'extras' => ['icon' => App::icons('globe')]
        ]);
    }

    /**
     * @param ItemInterface $menu
     */
    private function menuSupport(ItemInterface &$menu)
    {
        $menu->addChild('Support', [
            'uri' => 'http://suporte.inovadorsolar.com/',
            'extras' => ['icon' => App::icons('support')],
            'attributes' => [
                'class' => 'support_link'
            ]
        ]);
    }

    /**
     * @return \AppBundle\Entity\UserInterface
     */
    private function getUser()
    {
        /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->container->get('security.token_storage');

        return $tokenStorage->getToken()->getUser();
    }

    private function getRequest()
    {
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');

        return $requestStack->getCurrentRequest();
    }

    /**
     * Resolve active menu based request pathInfo
     * @param ItemInterface $menu
     */
    private function resolveActiveMenu(ItemInterface &$menu)
    {
        $uri = $this->getRequest()->getPathInfo();

        if($uri == $menu->getUri()){
            if($menu->getParent() && !$menu->getParent()->isRoot()){
                $menu->getParent()->setAttribute('class', 'active');
            }
            $menu->setAttribute('class', 'active');
        }elseif($menu->hasChildren()){
            foreach($menu->getChildren() as $child){
                $this->resolveActiveMenu($child);
            }
        }
    }
}