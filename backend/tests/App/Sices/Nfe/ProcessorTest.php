<?php

use App\Sices\Nfe\Parser;
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

    public function testMatchReference()
    {
        $manager = $this->manager('order');

        $files = [
            "3517111777450100012855001000010212100531131017110900920171123S",
            "3517111777450100012855001000010213100466462417111308520171123S",
            "3517111777450100012855001000010214100223243017111300320171123S",
            "3517111777450100012855001000010215100535935817110801220171123S",
            "3517111777450100012855001000010216100417411417103005420171123S"
        ];

        $parse = new Parser();
        $filter = new Processor($manager);

        foreach ($files as $file) {
            $danfe = $parse::extract($file);
            $order = $filter->matchReference($danfe);

            if (!$order) {
                continue;
            }
            var_dump($order);
        }

        die;
    }
}
