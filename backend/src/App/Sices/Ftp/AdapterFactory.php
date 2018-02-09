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

use Gaufrette\Adapter\Ftp as FtpAdapter;

/**
 * Class AdapterFactory
 * This class create a graufrette ftp instance based on configuration
 */
abstract class AdapterFactory
{
    /**
     * @param array $config
     * @return FtpAdapter
     */
    public static function create(array $config = [])
    {
        $host = $config['host'];
        $directory = $config['directory'];

        unset($config['host'], $config['directory']);

        $options = array_merge([
            'port'     => 21,
            'passive'  => true,
            'create'   => true,
            'mode'     => FTP_BINARY,
            'ssl'      => false,
        ], $config);

        return new FtpAdapter($directory, $host, $options);
    }
}
