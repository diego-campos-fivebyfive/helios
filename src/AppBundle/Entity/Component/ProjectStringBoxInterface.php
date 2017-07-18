<?php

namespace AppBundle\Entity\Component;

/**
 * Interface ProjectStringBoxInterface
 *
 * @author João Zaqueu <joaozaqueu@kolinalabs.com>
 */
interface ProjectStringBoxInterface extends ProjectElementInterface
{
    /**
     * @param StringBoxInterface $stringBox
     * @return ProjectStringBoxInterface
     */
    public function setStringBox(StringBoxInterface $stringBox);

    /**
     * @return StringBoxInterface
     */
    public function getStringBox();
}