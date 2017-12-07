<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Additive;

use AppBundle\Entity\Misc\Additive;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Synchronizer
 * This class reads registered additives and synchronizes
 * the associations according to the status of the additive
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Synchronizer
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Synchronizer constructor.
     * @param EntityManagerInterface $manager
     */
    function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $source
     */
    public function synchronize(&$source)
    {
        $this->validate($source);

        $class = get_class($source);
        $target = substr($class, strrpos($class, '\\')+1);
        $property = sprintf('%sAdditive', $target);
        $getMethod = sprintf('get%ss', $property);
        $removeMethod = sprintf('remove%s', $property);

        /** @var \AppBundle\Entity\Misc\AdditiveRelationTrait $association */
        foreach ($source->$getMethod() as $association){

            /** @var Additive $additive */
            $additive = $association->getAdditive();

            if(!$additive->isEnable()){
                $source->$removeMethod($association);
                $this->manager->remove($association);
            }
        }

        $this->manager->flush();
    }

    /**
     * @param $source
     */
    private function validate($source)
    {
        if(!is_object($source))
            $this->exception('Invalid source object');
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}
