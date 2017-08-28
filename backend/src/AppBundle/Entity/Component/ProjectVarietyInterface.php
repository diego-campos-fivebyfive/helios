<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

/**
 * Interface ProjectVarietyInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface ProjectVarietyInterface extends ProjectElementInterface
{
    /**
     * @param VarietyInterface $variety
     * @return ProjectVarietyInterface
     */
    public function setVariety(VarietyInterface $variety);

    /**
     * @return VarietyInterface
     */
    public function getVariety();
}