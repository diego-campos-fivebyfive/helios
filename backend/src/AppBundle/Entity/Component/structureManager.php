<?php

namespace AppBundle\Entity\Component;

use Sonata\CoreBundle\Model\BaseEntityManager;

class StructureManager extends BaseEntityManager implements StructureManagerInterface
{
    public function refreshTokens()
    {
        /*
        $inverters = $this->findBy(['token' => ''], null, 1000);
        if(!count($inverters))
            return;

        $inverter = $inverters[0];
        foreach ($inverters as $inverter) {
            if ($inverter instanceof InverterInterface) {
                if (!strlen($inverter->getToken()) || !$inverter->getToken()) {
                    $inverter->setProtectionClass('Undefined');
                    $this->save($inverter, false);
                }
            }
        }
        $this->save($inverter);
        dump('Last Inverter: ' . $inverter->getId());
        */
    }
}
