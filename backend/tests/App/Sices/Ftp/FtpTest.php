<?php

namespace Tests\App\Sices\Utils;

use App\Proceda\Parser;
use App\Sices\Ftp\FileSystemFactory;
use Tests\App\Sices\SicesTest;

/**
 * @group sices_ftp
 */
class FtpTest extends SicesTest
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * Setup test
     */
    public function setUp()
    {
        parent::setUp();
        $this->resolveFtpParameters();
    }

    /**
     * Test FTP fileSystem factory
     */
    public function testFtpInstance()
    {
        $fileSystem = FileSystemFactory::create($this->config['ftp']);

        $this->assertInstanceOf(\Gaufrette\Filesystem::class, $fileSystem);

        $this->assertTrue(is_array($fileSystem->keys()));
    }

    /**
     * Test SFTP fileSystem factory
     */
    public function testSftpInstance()
    {
        $fileSystem = FileSystemFactory::create($this->config['sftp']);

        $this->assertInstanceOf(\Gaufrette\Filesystem::class, $fileSystem);
    }

    /**
     * Resolve connection config for FTP and SFTP
     */
    private function resolveFtpParameters()
    {
        $cacheDir = $this->getContainer()->get('kernel')->getCacheDir();
        $filename = sprintf('%s/ftp_test.json', $cacheDir);

        if(!file_exists($filename)){
            throw new \BadMethodCallException(sprintf('Add ftp_test.json on [%s] dir', $cacheDir)); die;
        }

        $config = json_decode(file_get_contents($filename), true);

        $this->testRecursiveConfigKeys($config);

        $this->config = $config;
    }

    /**
     * @param array $data
     */
    private function testRecursiveConfigKeys(array $data)
    {
        /**
         * Expected reading output
         * TODO: Para testes, gerar um arquivo json no diretório de cache contendo as configurações no formato indicado.
         * TODO: Este procedimento é necessário para evitar exposição de credenciais de acesso diretamente no código de teste.
         */
        $expectedOutputFormat = [
            'ftp' => [
                'host' => '',
                'port' => 21,
                'username' => '',
                'password' => '',
                'directory' => '',
            ],
            'sftp' => [
                'host' => '',
                'port' => 22,
                'username' => '',
                'password' => '',
                'directory' => '',
            ]
        ];

        foreach ($expectedOutputFormat as $type => $config){
            $this->assertArrayHasKey($type, $data);
            $this->assertEquals(array_keys($config), array_keys($data[$type]));
        }
    }
}
