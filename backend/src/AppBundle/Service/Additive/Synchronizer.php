<?php

namespace AppBundle\Service\Additive;

use AppBundle\Entity\Misc\Additive;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Synchronizer
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $accessor;

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
        $this->accessor = PropertyAccess::createPropertyAccessor();
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
        $getCollection = sprintf('get%ss', $property);
        $removeMethod = sprintf('remove%s', $property);

        foreach ($source->$getCollection() as $association){

            /** @var Additive $additive */
            $additive = $association->getAdditive();

            if(!$additive->isEnabled()){
                $source->$removeMethod($association);
                $this->manager->remove($association);
            }
        }
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
