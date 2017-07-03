<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\MakerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineListener implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AppListener constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postRemove
        ];
    }

    /**
     * @param LifecycleEventArgs | \Doctrine\ORM\Event\PreUpdateEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();


        /**
         * Change component status to featured if one or more fields changed
         */
        if($entity instanceof ComponentInterface
            && $entity->isPrivate()){

            $changeSet = $args->getEntityChangeSet();
            foreach($changeSet as $property => $values){
                if('status' != $property && $values[0] != $values[1]){
                    $entity->setStatus(ComponentInterface::STATUS_FEATURED);
                    break;
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof ComponentInterface){

            $maker = $entity->getMaker();

            $components = $maker->isMakerModule() ? $maker->getModules() : $maker->getInverters() ;

            if((0 == $components->count()) && MakerInterface::REMOVE_ZERO_CHILD){
                $this->container->get('app.maker_manager')->delete($maker);
            }
        }
    }
}