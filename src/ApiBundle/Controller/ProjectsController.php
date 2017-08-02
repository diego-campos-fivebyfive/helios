<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectExtraInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

class ProjectsController extends FOSRestController
{
    public function getProjectsAction(Project $id)
    {
        $project = $id;

        $data = [
            'id' => $project-> getId(),
            'order' => $project->getOrder(),
            'address' => $project->getAddress(),
            'area' => $project->getArea(),
            'inverters' => $project->getProjectInverters(),
            'modules' => $project->getProjectModules(),
            'structures' => $project->getProjectStructures(),
            'stringBoxes' => $project->getProjectStringBoxes(),
            'Varietys' => $project->getProjectVarieties(),
        ];

        $view = View::create($data);

        return $this->handleView($view);
    }
}