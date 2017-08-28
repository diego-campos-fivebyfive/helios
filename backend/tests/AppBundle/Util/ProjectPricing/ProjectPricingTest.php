<?php

namespace Tests\AppBundle\Util\ProjectPricing;

use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Component\ProjectExtra;
use AppBundle\Util\ProjectPricing\CostPrice;
use Tests\AppBundle\AppTestCase;

/**
 * Class ProjectPricingTest
 * @group project_pricing
 */
class ProjectPricingTest extends AppTestCase
{
    public function testSaleMargins()
    {
        $product = $this->getFixture('component-extra-product');
        $service = $this->getFixture('component-extra-service');
        $inverter = $this->getFixture('inverter');
        $module = $this->getFixture('module');

        /** @var ProjectInterface $project */
        $project = $this->getFixture('project');

        $project->setTaxPercent(.1);

        $projectProduct = new ProjectExtra();
        $projectProduct
            ->setQuantity(5)
            ->setProject($project)
            ->setExtra($product);

        $projectService = new ProjectExtra();
        $projectService
            ->setProject($project)
            ->setQuantity(5)
            ->setExtra($service);

        /** @var \AppBundle\Entity\Component\ProjectInverterInterface $projectInverter */
        foreach ($project->getProjectInverters() as $projectInverter) {

            $markup = new TempMarkup();
            $markup
                ->setInitialPower(1000)
                ->setFinalPower(5000)
                ->setMarkup(.1);

            $projectInverter->setMarkup($markup);
        }

        /** @var \AppBundle\Entity\Component\ProjectInverterInterface $projectModule */
        foreach ($project->getProjectModules() as $projectModule) {

            $markup = new TempMarkup();
            $markup
                ->setInitialPower(1000)
                ->setFinalPower(5000)
                ->setMarkup(.1);

            $projectModule->setMarkup($markup);
        }

        $this->getContainer()->get('project_manager')->save($project);

        $costPrice = new CostPrice();
        $costPrice->calculate($project);

    }
}