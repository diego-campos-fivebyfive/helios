<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 10/07/17
 * Time: 12:15
 */

namespace AppBundle\Entity\Pricing;


interface LevelInterface
{
    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getName();
}