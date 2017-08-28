<?php

namespace AppBundle\Service\Component\Helper;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\InverterManagerInterface;

class InverterHelper
{
    /**
     * @var InverterManagerInterface
     */
    private $manager;

    function __construct(InverterManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param InverterInterface $baseInverter
     * @param BusinessInterface $account
     * @return InverterInterface
     */
    public function cloneToAccount(InverterInterface $baseInverter, BusinessInterface $account)
    {
        $inverter  = $this->copyInverter($baseInverter);

        $inverter->setAccount($account);

        $this->manager->save($inverter);

        return $inverter;
    }

    /**
     * @param InverterInterface $baseModule
     * @return InverterInterface
     */
    public function copyInverter(InverterInterface $baseInverter)
    {
        /** @var InverterInterface $inverter */
        $inverter = $this->manager->create();

        $ignores = self::getIgnoreCloneMethods();
        $methods = get_class_methods($this->manager->getClass());

        //dump($methods); die;

        foreach($methods as $method){
            if(0 === strpos($method, 'get') && !in_array($method, $ignores)){
                $setter = str_replace('get', 'set', $method);

                if(method_exists($inverter, $setter)){

                    $inverter->$setter($baseInverter->$method());
                }
            }
        }

        $inverter->setParent($baseInverter);
        $inverter->setStatus(InverterInterface::STATUS_FEATURED);

        return $inverter;
    }

    /**
     * @param ComponentInterface $component
     * @return InverterInterface
     */
    public function copyComponent(ComponentInterface $component)
    {
        if($component instanceof InverterInterface) {
            return $this->copyInverter($component);
        }

        throw new \InvalidArgumentException('Invalid inverter component');
    }

    /**
     * @return array
     */
    public static function getIgnoreCloneMethods()
    {
        return ['getId', 'getCreatedAt', 'getUpdatedAt', 'getAccount', 'getParent', 'getToken', 'getChildrens'];
    }
}