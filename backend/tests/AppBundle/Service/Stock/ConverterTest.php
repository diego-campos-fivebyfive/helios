<?php

namespace Tests\AppBundle\Service\Stock;

use AppBundle\Entity\Component\Structure;
use AppBundle\Entity\Stock\ProductInterface;
use AppBundle\Service\Stock\Converter;
use AppBundle\Service\Stock\Identity;
use AppBundle\Service\Stock\Provider;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class ConverterTest
 * @group stock
 * @group stock_converter
 */
class ConverterTest extends AppTestCase
{
    use ObjectHelperTest;

    // Multiple components
    public function testMultiTransformEntityToProduct()
    {
        $components = [
            $this->getFixture('inverter'),
            $this->getFixture('module'),
            $this->getFixture('string_box'),
            $this->getFixture('structure'),
            $this->getFixture('variety')
        ];

        $products = $this->getConverter()->transform($components);

        foreach ($products as $key => $product){
            $id = Identity::create($components[$key]);
            $this->assertInstanceOf(ProductInterface::class, $product);
            $this->assertEquals($id, $product->getId());
        }

        // step 2 - cached
        $cachedProducts = $this->getConverter()->transform($components);

        $this->assertEquals(count($products), count($cachedProducts));
    }

    /**
     * @return Converter
     */
    private function getConverter()
    {
        return $this->service('stock_converter');
    }
}
