<?php

namespace Tests\AppBundle\Service\Component;

use AppBundle\Entity\Component\Module;
use AppBundle\Service\Stock\Component;
use AppBundle\Service\Stock\Identity;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ComponentTest
 * @group stock_component
 */
class ComponentTest extends WebTestCase
{
    public function testTransactProcess()
    {
        $module = $this->getModule();

        /** @var Component $stockComponent */
        $stockComponent = $this->getContainer()->get('stock_component');

        $identity = Identity::create($module);

        $transactions = [
            ['identity' => $identity, 'amount' => 100, 'description' => 'Test'],
            ['identity' => $identity, 'amount' => 300, 'description' => 'Test']
        ];

        $stockComponent->transact($transactions);

        $this->getManager()->getObjectManager()->refresh($module);

        $this->assertEquals(400, $module->getStock());
    }

    /**
     * @return Module
     */
    private function getModule()
    {
        $code = 'TEST_MODULE';
        $manager = $this->getManager();

        if(null == $module = $manager->findOneBy(['code' => $code])){

            $module = $manager->create();
            $module->setCode($code);

            $manager->save($module);
        }

        return $module;
    }

    /**
     * @return \AppBundle\Manager\ModuleManager|object
     */
    private function getManager()
    {
        return $this->getContainer()->get('module_manager');
    }
}
