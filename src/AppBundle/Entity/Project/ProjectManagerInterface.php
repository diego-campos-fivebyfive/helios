<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\BusinessInterface;

interface ProjectManagerInterface
{
    /**
     * @param BusinessInterface $member
     * @return ProjectInterface
     */
    public function create(BusinessInterface $member = null);
}