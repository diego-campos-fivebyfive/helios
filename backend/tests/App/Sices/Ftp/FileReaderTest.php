<?php
/**
 * Created by PhpStorm.
 * User: kolinalabs
 * Date: 2/20/18
 * Time: 2:23 PM
 */

use Tests\App\Sices\SicesTest;
use App\Sices\Ftp\FileSystemFactory;
use App\Sices\Ftp\FileReader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FileReaderTest
 * @group sices_fileHeader
 */
class FileReaderTest extends SicesTest
{

    public function testDownload()
    {
        $fileSystem = FileSystemFactory::create([
            'host' => 'kolinalabs.com',
            'directory' => '/DANFE',
            'username' => 'sicesbrasil@kolinalabs.com',
            'password' => 'xVa0JZM}P4nf'
        ]);

        $file = 'PR180212345678000112AA123123456789C12345678B171010000013201802051.pdf';

        $fileReader = new FileReader($fileSystem, $this->getContainer());

        $download = $fileReader->download($file);

        self::assertNotNull($download);
    }

}
