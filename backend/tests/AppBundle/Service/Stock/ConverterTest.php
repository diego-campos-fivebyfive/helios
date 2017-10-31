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

    // Single component
    public function testTransformEntityToProduct()
    {
        $this->markTestSkipped();

        $converter = $this->getConverter();

        $module = $this->getFixture('module');

        $product = $converter->transform($module);

        $this->assertInstanceOf(ProductInterface::class, $product);
    }

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

    // Single product
    public function testReverseProductToEntity()
    {
        $this->markTestSkipped();

        // Get component
        $stringBox = $this->getFixture('string_box');

        $converter = $this->getConverter();

        // Transform component to product
        $product = $converter->transform($stringBox);

        $component = $converter->reverse($product->getId());
        $this->assertInstanceOf(Identity::getClass($stringBox), $component);
    }

    // Multiple products
    public function testMultiReverseProductToEntity()
    {
        $this->markTestSkipped();

        $components = [
            $this->getFixture('inverter'),
            $this->getFixture('module'),
            $this->getFixture('string_box'),
            $this->getFixture('structure'),
            $this->getFixture('variety')
        ];

        $converter = $this->getConverter();

        // Generate products
        $products = $converter->transform($components);

        $ids = array_map(function(ProductInterface $product){ return $product->getId(); }, $products);

        $reversedComponents = $converter->reverse($ids);

        $this->assertEquals($components, $reversedComponents);

        // Test reverse with product instance
        $reversedInstance = $converter->reverse($products[2]);

        $this->assertEquals($reversedComponents[2], $reversedInstance);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionOnAttemptTransformProduct()
    {
        $this->markTestSkipped();

        $converter = $this->getConverter();

        /** @var ProductInterface $product */
        $product = $converter->transform($this->getFixture('inverter'));

        $converter->transform($product);
    }

    public function testCacheResults()
    {
        $this->markTestSkipped();

        $totalStructures = 12;
        $structures = $this->createStructures($totalStructures);

        $converter = $this->getConverter();

        $products = $converter->transform($structures);

        $identities = $this->extractProductIds($products);

        $components = $converter->findComponents($identities);

        $this->assertCount($totalStructures, $components[Structure::class]);
    }

    /**
     * @return Converter
     */
    private function getConverter()
    {
        return $this->service('stock_converter');
    }

    /**
     * @param $quantity
     * @return array
     */
    private function createStructures($quantity)
    {
        $manager = $this->manager('structure');
        $components = [];
        for ($i = 0; $i < $quantity; $i++){

            $component = $manager->create();
            $component
                ->setCode(self::randomString(10))
                ->setDescription(self::randomString(50))
                ->setAvailable(true)
                ->setStatus(true)
            ;

            $manager->save($component);

            $components[] = $component;
        }

        return $components;
    }

    /**
     * @param $products
     * @return array
     */
    private function extractProductIds($products)
    {
        return array_map(function(ProductInterface $product){ return $product->getId(); }, $products);
    }
}
