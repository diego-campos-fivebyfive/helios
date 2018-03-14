<?php

use App\Sices\Nfe\Processor;
use Tests\AppBundle\AppTestCase;

/**
 * Class FileReaderTest
 * @group sices_nfe_processor
 */
class ProcessorNfeTest extends AppTestCase
{

    public function testIndexer()
    {
        $files = [
            "PR180212345678000112AA123123456789C12345678B171010000013201802051",
            "PR180212345678000112AA123123456789C12345678B171010000013201802051.xml",
            "PR180212345678000112AA123123456789C12345678B171010000013201802052.pdf",
            "PR180212345678000112AA123123456789C12345678B171010000013201802052.xml",
            "PR180212345678000112AA123123456789C12345678B171010000013201802053.pdf",
            "PR180212345678000112AA123123456789C12345678B171010000013201802053.xml",
            "3517111777450100012855001000012345100466462417112800820171120S.xml"
        ];

        $processor = new Processor($this->getContainer());

        $filesIndexed = $processor->indexer($files);

        self::assertCount(4, $filesIndexed);
        self::assertArrayHasKey(
            '3517111777450100012855001000012345100466462417112800820171120S',
            $filesIndexed
        );
        self::assertArrayNotHasKey('xml', $filesIndexed);
    }
}
