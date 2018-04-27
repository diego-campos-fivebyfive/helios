<?php

namespace Tests\AppBundle\Service\Additive;

use AppBundle\Entity\Misc\Additive;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Order\OrderAdditive;
use AppBundle\Entity\Pricing\Memorial;
use Tests\AppBundle\AppTestCase;

/**
 * Class TermsTest
 * @group terms_synchronize
 */
class TermsTest extends AppTestCase
{

    public function testSynchronize()
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
}
