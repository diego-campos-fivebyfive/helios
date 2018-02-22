<?php

namespace Tests\App\Sices\Utils;

use App\Sices\Ftp\FileReader;
use App\Sices\Ftp\FileSystemFactory;
use Tests\App\Sices\SicesTest;

/**
 * @group sices_ftp_prefixer
 */
class FtpTest extends SicesTest
{
    public function testFtpPrefixer()
    {
        $fileSystem = FileSystemFactory::create([
            'host' => 'kolinalabs.com',
            'directory' => '/PROCEDA-SICESSOLAR',
            'username' => 'sicesbrasil@kolinalabs.com',
            'password' => 'xVa0JZM}P4nf'
        ]);

        $fileReader = new FileReader($fileSystem);

        $success = $fileReader->prefixer('a1.TXT', 'PROCESSED_');

        self::assertEquals(true, $success);
    }
}