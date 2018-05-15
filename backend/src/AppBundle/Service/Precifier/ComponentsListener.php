<?php

namespace AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\MemorialManager;


/**
 * Class ComponentsListener
 * @package AppBundle\Service\Precifier
 * @author Gianluca Bine <gian_bine@hotmail.com>
 */
class ComponentsListener
{
    /** @var MemorialManager */
    private $manager;

    /**
     * @param MemorialManager $manager
     */
    function __construct(MemorialManager $manager)
    {
        $this->manager = $manager;
    }

    public function action($type, $family)
    {
        $memorials = $this->manager->findAll();

        /** @var Memorial $memorial */
        foreach($memorials as $memorial) {
            $memorial->addFamilyMetadata($type, $family);

            $this->manager->save($memorial, false);
        }

        $this->manager->flush();
    }
}