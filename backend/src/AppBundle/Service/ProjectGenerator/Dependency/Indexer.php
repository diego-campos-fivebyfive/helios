<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\ProjectGenerator\Dependency;

/**
 * Class Indexer
 * This class indexes dependencies by solving repetitions and grouping by types
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
abstract class Indexer
{
    /**
     * @param array $dependencies
     * @return array
     */
    public static function process(array $dependencies)
    {
        $output = [];
        foreach ($dependencies as $dependency){

            $type = $dependency['type'];
            $id = $dependency['id'];

            if(!array_key_exists($type, $output))
                $output[$type] = [];

            if(!array_key_exists($id, $output[$type])) {
                $output[$type][$id] = $dependency['ratio'];
            }else{
                $output[$type][$id] += $dependency['ratio'];
            }
        }

        return $output;
    }
}
