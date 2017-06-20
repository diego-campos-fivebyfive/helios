<?php

namespace AppBundle\Service\Component\Helper;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ModuleManagerInterface;

class ModuleHelper
{
    /**
     * @var ModuleManagerInterface
     */
    private $manager;

    function __construct(ModuleManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ModuleInterface $baseModule
     * @param BusinessInterface $account
     * @return object
     */
    public function cloneToAccount(ModuleInterface $baseModule, BusinessInterface $account)
    {
        $module  = $this->copyModule($baseModule);

        $module
            ->setAccount($account)
        ;

        $this->manager->save($module);

        return $module;
    }

    /**
     * @param ModuleInterface $baseModule
     * @return ModuleInterface
     */
    public function copyModule(ModuleInterface $baseModule)
    {
        /** @var Module $module */
        $module = $this->manager->create();

        $ignores = self::getIgnoreCloneMethods();
        $methods = get_class_methods($this->manager->getClass());

        foreach($methods as $method){
            if(0 === strpos($method, 'get') && !in_array($method, $ignores)){
                $setter = str_replace('get', 'set', $method);

                if(method_exists($module, $setter)){

                    $module->$setter($baseModule->$method());
                }
            }
        }

        $module->setParent($baseModule);
        $module->setStatus(ModuleInterface::STATUS_FEATURED);

        return $module;
    }

    /**
     * @param ComponentInterface $component
     * @return ModuleInterface
     */
    public function copyComponent(ComponentInterface $component)
    {
        if($component instanceof ModuleInterface) {
            return $this->copyModule($component);
        }

        throw new \InvalidArgumentException('Invalid module component');
    }

    /**
     * @return array
     */
    public static function getIgnoreCloneMethods()
    {
        return ['getId', 'getCreatedAt', 'getUpdatedAt', 'getAccount', 'getParent', 'getToken', 'getChildrens'];
    }
}