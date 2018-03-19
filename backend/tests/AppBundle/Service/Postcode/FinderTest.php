<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\Element;
use AppBundle\Service\Order\ElementResolver;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @group element_resolver
 */
class ElementResolverTest extends WebTestCase
{
    public function testResolveElementAndData()
    {
        $firstArg = null;   //  Instância desconhecida
        $secondArg = null;  //  Instância desconhecida

        $element = ElementResolver::resolve($firstArg, $secondArg);
        $this->assertInstanceOf(Element::class, $element);

        $firstArg = [];
        $secondArg = null;

        $element = ElementResolver::resolve($firstArg, $secondArg);
        $this->assertInstanceOf(Element::class, $element);

        $firstArg = new Element();
        $firstArg->setCode('ABC');
        $secondArg = [];

        $element = ElementResolver::resolve($firstArg, $secondArg);
        $this->assertInstanceOf(Element::class, $firstArg);
        $this->assertEquals($element->getCode(), $firstArg->getCode());
    }
}