<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\Item;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectAdditiveInterface;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectItem;
use AppBundle\Entity\Misc\AdditiveInterface;
use Proxies\__CG__\AppBundle\Entity\Misc\Additive;
use function Sodium\add;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class ProjectManagerTest
 * @group project_manager
 */
class ProjectManagerTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testProjectAssociations()
    {
        $additive = $this->createAdditive();

        $project = $this->createProject();

        $assoc = $this->createProjectAdditive($additive, $project);


        $this->assertNotNull($project->getId());
        $this->assertNotEmpty($project->getProjectAdditives()->toArray());

        /*$project->removeProjectAdditive($assoc);
        $this->assertEmpty($project->getProjectAdditives()->toArray());*/
    }

    private function createAdditive()
    {
        $insuranceManager = $this->manager('additive');

        /** @var Additive $insurance */
        $insurance = $insuranceManager->create();
        $insurance
            ->setName('Teste 1')
            ->setType(1)
            ->setTarget(2)
            ->setDescription('Descrição do seguro teste 1')
            ->setValue(0.65)
            ->setEnable(true)
        ;

        $insuranceManager->save($insurance);

        return $insurance;
    }

    private function createProjectAdditive($additive, $project)
    {
        $projectAssocManager = $this->manager('project_additive');

        $projectAdditive = $projectAssocManager->create();

        $projectAdditive
            ->setAdditive($additive)
            ->setProject($project);

        $projectAssocManager->save($projectAdditive);

        return $projectAdditive;
    }

    private function createProject()
    {
        $projectManager = $this->manager('project');

        /** @var Project $project */
        $project = $projectManager->create();

        $projectManager->save($project);

        return $project;
    }
}
