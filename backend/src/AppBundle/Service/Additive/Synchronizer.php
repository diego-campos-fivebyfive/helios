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
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $metadata;

    /**
     * Synchronizer constructor.
     * @param EntityManagerInterface $manager
     * @param ContainerInterface $container
     */
    function __construct(EntityManagerInterface $manager, ContainerInterface $container)
    {
        $this->manager = $manager;
        $this->container = $container;
    }

    /**
     * @deprecated
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
     * @deprecated
     * @param $source
     * @param $type
     * @return array
     */
    public function findBySource($source, $type)
    {
        $metadata = $this->getMetadata($source);

        $level = $metadata['level'];
        $related = $metadata['related'];
        $target = $metadata['target'];
        $targetProperty = strtolower($target);

        $qb = $this->manager->createQueryBuilder();

        $qb2 = $this->manager->createQueryBuilder();
        $qb2
            ->select('a2.id')
            ->from($related, 'r')
            ->join('r.additive', 'a2', 'WITH')
            ->where(sprintf('r.%s = :%s', $targetProperty, $targetProperty))
            ->setParameter($targetProperty, $source)
        ;

        $relatedIds = array_map('current', $qb2->getQuery()->getResult());
        $exprRelated = empty($relatedIds) ? null : $qb->expr()->in('a.id', $relatedIds);

        $qb->select('a')->from(Additive::class, 'a');

        $qb->where(
                $qb->expr()->orX(
                    $qb->where('a.enabled = 1')
                        ->expr()->like('a.availableLevels', $qb->expr()->literal('%"'.$level.'"%')),
                    $exprRelated
                )
            )
            ->groupBy('a.id')
        ;

        $qb->andWhere('a.type = :type')
            ->setParameter('type', $type);

        return $qb->getQuery()->getResult();
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
        )->andWhere('a.enabled = 1');

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
     * @return array
     */
    public function normalizeInsurances($source)
    {
        $this->validate($source);

        $metadata = $this->getMetadata($source);

        $qb = $this->queryAvailableInsurances($source, $metadata['target']);

        $availableInsurances = $qb->getQuery()->getResult();

        $this->resolveAssociations($source, $qb, $metadata, $availableInsurances);

        return $availableInsurances;
    }

    /**
     * @param $source
     * @param $qb
     * @param $metadata
     * @param $availableInsurances
     */
    private function resolveAssociations(&$source, $qb, $metadata, $availableInsurances)
    {
        $collectionGetter = $metadata['getter'];

        /** @var \Doctrine\Common\Collections\ArrayCollection $associations */
        $associations = $source->$collectionGetter();

        $associationsAdditiveIds = $associations->map(function($association){
            return $association->getAdditive()->getId();
        })->toArray();

        $insurancesRequiredId = array_map(function ($insurance){
            return $insurance->getId();
        },$qb->andWhere(
            $qb->expr()->like('a.requiredLevels',
                $qb->expr()->literal('%"' . $metadata['level'] . '"%')
            )
        )->getQuery()->getResult()
        );

        $related = $metadata['related'];
        $sourceSetter = sprintf('set%s', $metadata['target']);

        foreach ($availableInsurances as $insurance) {
            if (in_array($insurance->getId(), $insurancesRequiredId)) {
                if (!in_array($insurance->getId(), $associationsAdditiveIds)) {
                    /** @var AdditiveRelationTrait $association */
                    $association = new $related();

                    $association
                        ->setAdditive($insurance)
                        ->$sourceSetter($source)
                    ;
                }
            }

            if (in_array($insurance->getId(), $associationsAdditiveIds))
                unset($associationsAdditiveIds[array_search($insurance->getId(), $associationsAdditiveIds)]);
        }

        $this->manager->persist($source);
        $this->manager->flush();

        $this->removeDeprecatedAssociation($source, $metadata, $associationsAdditiveIds);
    }

    /**
     * @param $source
     * @param $metadata
     * @param $deprecatedAssociations
     */
    private function removeDeprecatedAssociation($source, $metadata, $deprecatedAssociations)
    {
        $sourceAdditiveManager = $this->container->get($metadata['property'].'Manager');

        $propertySourceId = strtolower($metadata['target']);
        foreach (array_values($deprecatedAssociations) as $id) {
            $association = $sourceAdditiveManager->findOneBy([
                $propertySourceId => $source->getId(),
                'additive' => $id
            ]);

            $sourceAdditiveManager->delete($association);
        }
    }

    /**
     * @param $source
     * @param $target
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function queryAvailableInsurances(&$source, $target)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->select('a.id')->from(Additive::class, 'a');

        $power = $source->getPower();

        if ($target == 'Project')
            $price = $source->getCostPriceComponents();
        else
            $price = $source->getSubTotal();

        $level = $source->getLevel();

        $offTheRule = array_map('current',
            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->gt('a.minPower', $power),
                    $qb->expr()->lt('a.maxPower', $power),
                    $qb->expr()->gt('a.minPrice', $price),
                    $qb->expr()->lt('a.maxPrice', $price)
                )
            )->getQuery()->getResult()
        );

        $qb->select('a')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('a.enabled', 1),
                    $qb->expr()->like('a.availableLevels', $qb->expr()->literal('%"'.$level.'"%'))
                )
            );

        $qb->andWhere('a.type = :type')
            ->setParameter('type', Additive::TYPE_INSURANCE);

        if ($offTheRule)
            $qb->andWhere($qb->expr()->notIn('a.id', $offTheRule));

        return $qb;
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
        $level = $source->getLevel();

        return [
            'class' => $class,
            'related' => $related,
            'target' => $target,
            'property' => $property,
            'adder' => $adder,
            'getter' => $getter,
            'remover' => $remover,
            'level' => $level
        ];
    }
}
