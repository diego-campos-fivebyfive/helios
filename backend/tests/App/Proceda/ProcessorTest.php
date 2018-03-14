<?php

use App\Sices\Ftp\FileSystemFactory;
use Tests\AppBundle\AppTestCase;

/**
 * @group proceda_processor
 */
class ProcessorTest extends AppTestCase
{
    /** @var  \App\Proceda\Processor */
    private $processor;

    /** @var  \App\Sices\Ftp\FileReader */
    private $fileReader;

    /**
     * Before tests
     * @inheritDoc
     */
    public function setUp()
    {
        $this->processor = $this->service('proceda_processor');

      //  $this->initializeScenario();
    }

    /**
     * Test file manipulation before and after resolve
     */
    public function testMoveFilesProcess()
    {
       // $this->assertCount(3, $this->fileReader->files(\App\Proceda\Processor::SEARCH_PREFIX));
       // $this->assertCount(3, $this->fileReader->files(\App\Proceda\Processor::PROCESSED_DIR));

        $orderManager = $this->getContainer()->get('order_manager');

        /** @var \AppBundle\Entity\Order\Order $order */
        $order = $orderManager->create();

        $order->addInvoice('000011975');

        $orderManager->save($order);
        $order2 = $orderManager->create();

        $order2->addInvoice('000012345');

        $orderManager->save($order2);

        $status = $this->processor->resolve();

        self::assertEquals($status['loaded_files'], 1);
        self::assertEquals($status['loaded_events'], 16);
        self::assertEquals($status['cached_events'], 14);

//        $this->assertCount(1, $this->fileReader->files(\App\Proceda\Processor::SEARCH_PREFIX));
//        $this->assertCount(5, $this->fileReader->files(\App\Proceda\Processor::PROCESSED_DIR));
    }

//    /**
//     * - Create FileReader instance
//     * - Remove all files
//     * - Create new files
//     */
//    private function initializeScenario()
//    {
//        $fileSystem = FileSystemFactory::create([
//            'host' => $this->getContainer()->getParameter('ftp_host'),
//            'port' => $this->getContainer()->getParameter('ftp_port'),
//            'username' => $this->getContainer()->getParameter('ftp_user'),
//            'password' => $this->getContainer()->getParameter('ftp_password'),
//            'directory' => '/ftp/PROCEDA-SICESSOLAR'
//        ]);
//
//        $this->fileReader = new \App\Sices\Ftp\FileReader();
//        $this->fileReader->init($fileSystem);
//
//        $currentFiles = $this->fileReader->files();
//
//        foreach ($currentFiles as $currentFile){
//            if(!$this->fileReader->isDirectory($currentFile)) {
//                $this->fileReader->delete($currentFile);
//            }
//        }
//
//        $files = [
//            'OCOREN-ONE.TXT',
//            'OCOREN-TWO.TXT',
//            'OCOREN-THREE.TXT',
//            'OTHER-FILE-TYPE.TXT',          // non-standard - will not be handled
//            'PROCESSED/OCOREN-ONE.TXT',     // duplicate - will not be handled
//            'PROCESSED/OCOREN-FOUR.TXT',
//            'PROCESSED/OCOREN-FIVE.TXT'
//        ];
//
//        foreach ($files as $file){
//            $this->fileReader->write($file, $file);
//        }
//    }
}
