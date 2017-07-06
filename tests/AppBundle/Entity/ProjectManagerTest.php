<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\ProjectArea;
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
        /** @var ProjectArea $projectArea */
        $projectArea = $this->getFixture('project-area');

        $project = $projectArea->getProjectModule()->getProject();

        $this->assertNotNull($project->getCustomer());
    }
}