<?php

namespace Tests\AppBundle\Service\Additive;

use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderAdditive;
use AppBundle\Entity\Pricing\Memorial;
use Tests\AppBundle\AppTestCase;

/**
 * Class SynchronizerTest
 * @group additive_sync
 */
class SynchronizerTest extends AppTestCase
{

    public function testGetUnlinkedAdditivesByRequiredLevels()
    {
        $manager = $this->manager('order');
        $synchronizer = $this->getSynchronizer();

        $partnerAdditives = $this->createAdditives(5, [
            'requiredLevels' => ['partner']
        ]);

        $order = $manager->create();
        $order->setLevel(Memorial::LEVEL_PARTNER);

        $addPartnerAdditives = array_slice($partnerAdditives, 0, 3);
        $this->assertCount(3, $addPartnerAdditives);

        $this->associateAdditives($order, $addPartnerAdditives);

        $partnerAdditivesLoaded = $synchronizer->getUnlinkedAdditivesByRequiredLevels($order);

        $this->assertCount(2, $partnerAdditivesLoaded);
    }

    public function testDefaultSynchronization()
    {
        $manager = $this->manager('order');

        /** @var Order $order */
        $order = $manager->create();
        $order->setLevel(Memorial::LEVEL_PREMIUM);

        $additives = $this->createAdditives(10, [
            'requiredLevels' => [Memorial::LEVEL_PREMIUM]
        ]);

        $this->associateAdditives($order, array_slice($additives, 0, 5));

        $synchronizer = $this->getSynchronizer();

        $manager->save($order);

        $this->assertCount(5, $order->getOrderAdditives()->toArray());

        $synchronizer->synchronize($order);

        $this->assertCount(10, $order->getOrderAdditives()->toArray());

        $this->testProject($additives);
    }

    /**
     * @return array
     */
    private function createAdditives($count = 10, array $definitions = [])
    {
        $manager = $this->manager('additive');

        $additives = [];
        for($i=0; $i < $count; $i++){

            /** @var Additive $additive */
            $additive = $manager->create();

            $additive
                ->setName('Additive ' . ($i+1))
                ->setDescription('This is a test additive ' . ($i+1))
                ->setEnabled(true)
                ->setTarget(Additive::TARGET_FIXED)
                ->setType(Additive::TYPE_INSURANCE)
                ->setValue(100);

            foreach ($definitions as $definition => $value){
                $this->accessor->setValue($additive, $definition, $value);
            }

            $manager->save($additive);

            $additives[] = $additive;
        }

        return $additives;
    }

    /**
     * @param Order $order
     * @param array $additives
     */
    private function associateAdditives(Order $order, array $additives)
    {
        foreach ($additives as $additive){

            $orderAdditive = new OrderAdditive();

            $orderAdditive
                ->setAdditive($additive)
                ->setOrder($order)
            ;
        }
    }

    /**
     * @return object|\AppBundle\Service\Additive\Synchronizer
     */
    private function getSynchronizer()
    {
        return $this->service('additive_synchronizer');
    }

    /**
     * Test Project additives sync functionality
     * @param array $additives
     */
    private function testProject(array $additives)
    {
        $project = $this->manager('project')->create();
        $project->setLevel(Memorial::LEVEL_PREMIUM);

        $this->assertCount(0, $project->getProjectAdditives()->toArray());

        $synchronizer = $this->getSynchronizer();

        $synchronizer->synchronize($project);

        $this->assertCount(10, $project->getProjectAdditives()->toArray());
    }
}
