<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Sices\Ftp;

use Gaufrette\Filesystem;

/**
 * Class FileSystemFactory
 * This class create a graufrette fylesystem based on configuration
 */
abstract class FileSystemFactory
{
    /**
     * @param array $config
     * @return Filesystem
     */
    public static function create(array $config = [])
    {
        $adapter = AdapterFactory::create($config);

        return new Filesystem($adapter);
    }
}
