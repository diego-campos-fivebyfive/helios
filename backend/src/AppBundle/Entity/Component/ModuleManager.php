<?php

namespace AppBundle\Entity\Component;

use Sonata\CoreBundle\Model\BaseEntityManager;

class ModuleManager extends BaseEntityManager implements ModuleManagerInterface
{
    public function refreshTokens()
    {
        /*$modules = $this->findBy(['token' => ''], null, 1000);
        if(!count($modules))
            return;
        $module = $modules[0];
        foreach ($modules as $module) {
            if ($module instanceof ModuleInterface) {
                if (!strlen($module->getToken()) || !$module->getToken()) {
                    $module->setGlassThickness(1);
                    $this->save($module, false);
                }
            }
        }
        $this->save($module);*/
    }
}
