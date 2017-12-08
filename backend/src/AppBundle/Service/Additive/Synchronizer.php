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
use AppBundle\Entity\Misc\AdditiveRelationTrait;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Synchronizer
 * This class reads registered additives and synchronizes
 * the associations according to the status and levels of the additive
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
     * @var array
     */
    private $metadata;

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

        $metadata = $this->getMetadata($source);

        $related = $metadata['related'];
        $sourceSetter = sprintf('set%s', $metadata['target']);

        $unlinkedAdditives = $this->getUnlinkedAdditivesByRequiredLevels($source);

        foreach ($unlinkedAdditives as $additive){

            /** @var AdditiveRelationTrait $association */
            $association = new $related();

            $association
                ->setAdditive($additive)
                ->$sourceSetter($source)
            ;
        }

        $this->manager->persist($source);
        $this->manager->flush();
    }

    /**
     * @param $source
     * @return array
     */
    public function getUnlinkedAdditivesByRequiredLevels($source)
    {
        $this->validate($source);

        $metadata = $this->getMetadata($source);

        $collectionGetter = $metadata['getter'];

        /** @var \Doctrine\Common\Collections\ArrayCollection $associations */
        $associations = $source->$collectionGetter();

        $associationsAdditiveIds = $associations->map(function($association){
            return $association->getAdditive()->getId();
        })->toArray();

        $level = $source->getLevel();

        $qb = $this->manager->createQueryBuilder();

        $qb->select('a')->from(Additive::class, 'a');

        $qb->where(
            $qb->expr()->like('a.requiredLevels',
                $qb->expr()->literal('%"' . $level . '"%')
            )
        );

        if(!empty($associationsAdditiveIds)){
            $qb->andWhere(
                $qb->expr()->notIn('a.id', $associationsAdditiveIds)
            );
        }

        $additives = $qb->getQuery()->getResult();

        return $additives;
    }

    /**
     * @param $source
     */
    private function validate($source)
    {
        if(!is_object($source))
            $this->exception('Invalid source object');

        if(!method_exists($source, 'getLevel'))
            $this->exception('The object does not have the getLevel');

        if(!$source->getLevel())
            $this->exception('Object level is invalid');
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }

    /**
     * @param object $source
     * @return array
     */
    private function getMetadata($source)
    {
        if($this->metadata)
            return $this->metadata;

        $class = get_class($source);
        $related = sprintf('%sAdditive', $class);
        $target = substr($class, strrpos($class, '\\')+1);
        $property = sprintf('%sAdditive', $target);
        $getter = sprintf('get%ss', $property);
        $remover = sprintf('remove%s', $property);
        $adder = sprintf('add%s', $property);

        return [
            'class' => $class,
            'related' => $related,
            'target' => $target,
            'property' => $property,
            'adder' => $adder,
            'getter' => $getter,
            'remover' => $remover
        ];
    }
}
