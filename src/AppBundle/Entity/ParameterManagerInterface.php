<?php

namespace AppBundle\Entity;

use Sonata\CoreBundle\Model\ManagerInterface;

interface ParameterManagerInterface extends ManagerInterface
{
    /**
     * @param $id
     * @return Parameter
     */
    public function findOrCreate($id);
}