<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Stock;

use Doctrine\Common\Util\ClassUtils;

/**
 * Class Identity
 * This class generates identification strings for entities
 * Accept: Object entity or array of object entities
 *
 * SAMPLES
 *
 * Single:
 * Call: Identity::create($inverter); # id = 5
 * Output: string
 * AppBundle\Entity\Component\Inverter::5
 *
 * Multiple:
 * Call: Identity::create([$module, $structure1, $structure2]); # ids = 11, 15, 253
 * Output: array
 * [
 *      'AppBundle\Entity\Component\Module::11',
 *      'AppBundle\Entity\Component\Structure::15',
 *      'AppBundle\Entity\Component\Structure::253'
 * ]
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Identity
{
    /**
     * @param $object
     * @return string
     */
    public static function create($source)
    {
        self::validate($source);

        if(is_array($source)){

            $ids = [];
            foreach ($source as $object){
                $ids[] = self::create($object);
            }

            return $ids;
        }

        return sprintf('%s::%s', ClassUtils::getClass($source), $source->getId());
    }

    /**
     * @param $object
     */
    private static function validate($source)
    {
        if(is_array($source)) return;

        if(!is_object($source))
            throw new \InvalidArgumentException('Only objects can be identified.');

        if(!method_exists($source, 'getId'))
            throw new \InvalidArgumentException('The object has no identifier method [getId].');
    }
}
