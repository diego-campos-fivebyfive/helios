<?php

namespace AppBundle\Service\ProjectGenerator;

use AppBundle\Manager\ModuleManager;

class ModuleProvider
{
    public static $criteria = [
        'status' => true,
        'available' => true
    ];

    /**
     * @var ModuleManager
     */
    private $manager;

    /**
     * @param ModuleManager $manager
     */
    function __construct(ModuleManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array
     */
    public function getAvailable()
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->where('m.status = :status')
        ->andWhere('m.available = :available')
        ->setParameters(self::$criteria);

        return $qb->getQuery()->getResult();
    }
}