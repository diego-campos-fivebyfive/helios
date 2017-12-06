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


interface ProjectAdditiveInterface
{
    /**
     * @param ProjectInterface $project
     * @return ProjectAdditiveInterface
     */
    public function setProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProject();

    /**
     * @return float
     */
    public function getAdditiveQuota();
}
