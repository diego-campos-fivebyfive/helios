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
 * Class AdapterFactory
 * This class create a graufrette ftp instance based on configuration
 */
class FileReader
{
    private $fileSystem;

    /**
     * FileReader constructor.
     * @param Filesystem $fileSystem
     */
    function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    function __call($method, $arguments)
    {
        if(method_exists($this->fileSystem, $method)){
            return call_user_func_array([$this->fileSystem, $method], $arguments);
        }

        throw new \BadMethodCallException(sprintf('The method %s does not exist', $method));
    }

    /**
     * @param string $prefix
     * @return mixed
     */
    public function files($prefix = '')
    {
        if(!strlen($prefix))
            return $this->keys();

        return $this->listKeys($prefix)['keys'];
    }

    /**
     * @param $file
     * @param bool $returnPath
     * @return string
     */
    public function download($file)
    {
        $content = $this->fileSystem->read($file);
        $path = dirname(__FILE__) . '/storage/' . $file;

        $handle = fopen($path, 'w+');

        fwrite($handle, $content);
        fclose($handle);

        return $path;
    }
}
