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
        ];

        $processor = new Processor();

        $filesIndexed = $processor->indexer($files);

        self::assertCount(3, $filesIndexed);
        self::assertArrayHasKey(
            'PR180212345678000112AA123123456789C12345678B171010000013201802051',
            $filesIndexed
        );
        self::assertArrayNotHasKey('xml', $filesIndexed);
    }
}
