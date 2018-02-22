<?php

use App\Sices\Nfe\Processor;
use Tests\App\Sices\SicesTest;

/**
 * Class FileReaderTest
 * @group sices_nfe_processor
 */
class ProcessorTest extends SicesTest
{

    public function testIndexer()
    {
        $filesList = [
            "PR180212345678000112AA123123456789C12345678B171010000013201802051",
            "PR180212345678000112AA123123456789C12345678B171010000013201802051.xml",
            "PR180212345678000112AA123123456789C12345678B171010000013201802052.pdf",
            "PR180212345678000112AA123123456789C12345678B171010000013201802052.xml",
            "PR180212345678000112AA123123456789C12345678B171010000013201802053.pdf",
            "PR180212345678000112AA123123456789C12345678B171010000013201802053.xml",
        ];

        $processor = new Processor();

        $arrayIndexed = $processor->indexer($filesList);

        self::assertCount(3, $arrayIndexed);
        self::assertArrayHasKey(
            'PR180212345678000112AA123123456789C12345678B171010000013201802051',
            $arrayIndexed
        );
        self::assertArrayNotHasKey('xml', $arrayIndexed);
    }
}
