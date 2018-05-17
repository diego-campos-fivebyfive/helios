<?php

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Manager\Precifier\MemorialManager;
use AppBundle\Service\Precifier\Calculator;
use AppBundle\Service\Precifier\ComponentsListener;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ComponentsListenerTest
 * @group precifier_components_listener
 */
class ComponentsListenerTest extends WebTestCase
{
    public function testAction()
    {
        /** @var ComponentsListener $componentsLister */
        $componentsLister = $this->getContainer()->get('precifier_components_listener');

        $componentsLister->action(Memorial::ACTION_TYPE_ADD_COMPONENT, 'module');
        $componentsLister->action(Memorial::ACTION_TYPE_REMOVE_COMPONENT, 'variety');

        /** @var MemorialManager $memorialManager */
        $memorialManager = $this->getContainer()->get('precifier_memorial_manager');

        $memorials = $memorialManager->findAll();

        /** @var Memorial $memorial */
        foreach ($memorials as $memorial) {
            self::assertArrayHasKey(Memorial::ACTION_TYPE_ADD_COMPONENT, $memorial->getMetadata());
            self::assertArrayHasKey(Memorial::ACTION_TYPE_REMOVE_COMPONENT, $memorial->getMetadata());
            self::assertArrayHasKey('module', $memorial->getMetadata()[Memorial::ACTION_TYPE_ADD_COMPONENT]);
            self::assertArrayHasKey('variety', $memorial->getMetadata()[Memorial::ACTION_TYPE_REMOVE_COMPONENT]);
        }
    }
}
