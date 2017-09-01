<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Parameter;
use AppBundle\Entity\ParameterManagerInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

class ParameterManager extends BaseEntityManager implements ParameterManagerInterface
{
    /**
     * @param $id
     * @return Parameter
     */
    public function findOrCreate($id)
    {
        $parameter = $this->findOneBy(['id' => $id]);

        if(!$parameter){
            $parameter = $this->create();
            $parameter->setId($id);
        }

        return $parameter;
    }
}