<?php

namespace AppBundle\Entity\Component;

interface ProjectStructureInterface extends ProjectElementInterface
{
    /**
     * @param StructureInterface $structure
     * @return ProjectStructureInterface
     */
    public function setStructure(StructureInterface $structure);

    /**
     * @return StructureInterface
     */
    public function getStructure();
}