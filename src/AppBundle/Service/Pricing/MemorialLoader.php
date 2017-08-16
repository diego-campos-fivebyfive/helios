<?php

namespace AppBundle\Service\Pricing;

use AppBundle\Manager\Pricing\MemorialManager;

class MemorialLoader
{
    /**
     * @var MemorialManager
     */
    private $manager;

    /**
     * @param MemorialManager $manager
     */
    function __construct(MemorialManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return \AppBundle\Entity\Pricing\MemorialInterface|object
     */
    public function load()
    {
        return $this->manager->find(101);
    }
}