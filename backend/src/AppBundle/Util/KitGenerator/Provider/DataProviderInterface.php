<?php
/**
 * Created by PhpStorm.
 * User: claudinei
 * Date: 21/06/17
 * Time: 12:45
 */

namespace AppBundle\Util\KitGenerator\Provider;


interface DataProviderInterface
{
    public function getMakers();

    //public function getModules($makerId);

    public function findModulesByMaker();
}