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

        $this->initializeScenario();
    }

    /**
     * Test file manipulation before and after resolve
     */
    public function testDefaultProcess()
    {
        /** @var \App\Proceda\Processor $processor */
        $processor = $this->service('proceda_processor');

        $this->assertTrue(is_array($processor->resolve()));
    }

    /**
     * - Create FileReader instance
     * - Remove all files
     * - Create new files
     */
    private function initializeScenario()
    {
        $fileSystem = FileSystemFactory::create([
            'host' => $this->getContainer()->getParameter('ftp_host'),
            'port' => $this->getContainer()->getParameter('ftp_port'),
            'username' => $this->getContainer()->getParameter('ftp_user'),
            'password' => $this->getContainer()->getParameter('ftp_password'),
            'directory' => '/PROCEDA-SICESSOLAR'
        ]);

        $this->fileReader = new \App\Sices\Ftp\FileReader();
        $this->fileReader->init($fileSystem);

        $currentFiles = $this->fileReader->files();

        foreach ($currentFiles as $currentFile){
            if(!$this->fileReader->isDirectory($currentFile)) {
                $this->fileReader->delete($currentFile);
            }
        }

        $files = [
            'OCOREN-ONE.TXT',
            'OCOREN-TWO.TXT',
            'OCOREN-THREE.TXT',
            'OTHER-FILE-TYPE.TXT',          // non-standard - will not be handled
            'PROCESSED/OCOREN-ONE.TXT',     // duplicate - will not be handled
            'PROCESSED/OCOREN-FOUR.TXT',
            'PROCESSED/OCOREN-FIVE.TXT'
        ];

        foreach ($files as $file){
            $this->fileReader->write($file, $file);
        }
    }
}
