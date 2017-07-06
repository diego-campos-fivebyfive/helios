<?php

namespace AppBundle\Manager;

use Sonata\CoreBundle\Model\BaseEntityManager;

class StructureManager extends BaseEntityManager implements StructureManagerInterface
{
    public function refreshTokens()
    {
        /*
        $structures = $this->findBy(['token' => ''], null, 1000);
        if(!count($structures))
            return;

        $structure = $structures[0];
        foreach ($structures as $structure) {
            if ($structure instanceof StructureInterface) {
                if (!strlen($structure->getToken()) || !$structure->getToken()) {
                    $structure->setProtectionClass('Undefined');
                    $this->save($structure, false);
                }
            }
        }
        $this->save($inverter);
        dump('Last Structure: ' . $structure->getId());
        */
    }
}
