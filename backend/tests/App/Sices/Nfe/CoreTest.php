<?php

use App\Sices\Nfe\Core;
use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;

/**
 * Class CoreNfeTest
 * @group sices_nfe_core
 */
class CoreNfeTest extends WebTestCase
{

    public function testCore()
    {
//        $orderManager = $this->getContainer()->get('order_manager');
//
//        $order = $orderManager->create();
//
//        $order->setReference('171128008');
//
//        $orderManager->save($order);

        $core = new Core($this->getContainer());

        $result = $core->core();

        $this->assertEquals($result['loaded_files'], 1);
        $this->assertEquals($result['processed_files'], 1);

//        $files = [
//            "3517111777450100012855001000012345100466462417112800820171120S.XML"
//        ];
//        $filesIndexed = $processor->indexer($files);
//
//        self::assertCount(1, $filesIndexed);
//        self::assertArrayHasKey(
//            '3517111777450100012855001000012345100466462417112800820171120S',
//            $filesIndexed
//        );
//        self::assertArrayNotHasKey('xml', $filesIndexed);
    }
}
