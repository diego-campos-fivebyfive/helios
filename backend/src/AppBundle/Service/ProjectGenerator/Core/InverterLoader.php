<?php

namespace AppBundle\Service\ProjectGenerator\Core;

use AppBundle\Manager\InverterManager;

/**
 * Class InverterLoader
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class InverterLoader
{
    /**
     * @var array
     */
    private $config = [
        'manager' => null,
        'maker' => null
    ];

    /**
     * @var InverterManager
     */
    private $manager;

    /**
     * InverterLoader constructor.
     * @param array $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
        $this->manager = $this->config['manager'];
    }

    /**
     * @param array $config
     * @return InverterLoader
     */
    public static function create(array $config)
    {
        return new self($config);
    }

    /**
     * @return array
     */
    public function all()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->where('i.maker = :maker')
            ->orderBy('i.nominalPower', 'ASC')
            ->setParameter('maker', $this->config['maker']);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function alternatives()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb2 = $this->manager->createQueryBuilder();

        $alternatives = array_map(function ($alt) {
            return current($alt);
        }, $qb2->select("DISTINCT(i.alternative)")
            ->where('i.alternative > 0')
            ->getQuery()->getResult()
        );

        if ($alternatives) {
            return $qb->where(
                $qb->expr()->andX(
                    $qb->expr()->in(
                        'i.id',
                        $alternatives
                    ),
                    'i.maker != :maker'
                ))
                ->orderBy('i.nominalPower', 'ASC')
                ->setParameter('maker', $this->config['maker'])
                ->getQuery()->getResult();
        }

        return [];
    }
}
