<?php

namespace Tests\AppBundle\Entity\DataFixtures\Component;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Component\Extra;
use Tests\AppBundle\Entity\DataFixtures\DataFixtureHelper;

class ExtraProductData extends AbstractFixture implements OrderedFixtureInterface
{
    use DataFixtureHelper;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            'pricingBy' => Extra::PRICING_FIXED,
            'type' => Extra::TYPE_PRODUCT,
            'description' => 'The extra product',
            'costPrice' => 100
        ];

        $extra = new Extra();

        $this->fillAndSave($extra, $data, $manager, 'component-extra-product');
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 3;
    }
}