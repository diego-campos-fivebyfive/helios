<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Entity\Component\VarietyInterface as TransformerInterface;
use AppBundle\Manager\VarietyManager as TransformerManager;

/**
 * TransformerLoader
 * This class load transformer by power
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class TransformerLoader
{
    /**
     * @var TransformerManager
     */
    private $manager;

    function __construct(TransformerManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $power
     * @return TransformerInterface
     */
    public function load($power)
    {
        $qb = $this->manager->getEntityManager()->createQueryBuilder();

        $qb
            ->select('t')
            ->from($this->manager->getClass(), 't')
            ->where('t.type = :type')
            ->andWhere('t.power >= :power')
            ->setMaxResults(1)
            ->setParameters([
                'type' => TransformerInterface::TYPE_TRANSFORMER,
                'power' => $power
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }
}