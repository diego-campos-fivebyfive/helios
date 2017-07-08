<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Component\Item;
use AppBundle\Entity\Component\ProjectArea;
use AppBundle\Entity\Component\ProjectItem;
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

        $items = $this->createItems();

        foreach ($items as $item) {
            $projectItem = new ProjectItem();
            $projectItem
                ->setItem($item)
                ->setProject($project)
            ;
        }

        $this->assertCount(count($items), $project->getProjectItems()->toArray());
    }

    private function createItems()
    {
        $items = [];
        $manager = $this->getContainer()->get('item_manager');
        for($i = 0; $i < 10; $i++){

            $data = [
                'type' => $i >= 5 ? Item::TYPE_PRODUCT : Item::TYPE_SERVICE,
                'description' => self::randomString(50),
                'pricingBy' => Item::PRICING_FIXED,
                'costPrice' => self::randomFloat() * 100
            ];

            $item = $manager->create();

            self::fluentSettersTest($item, $data);

            $manager->save($item, ($i == 9));

            $items[] = $item;
        }

        return $items;
    }
}