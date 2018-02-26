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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AdapterFactory
 * This class create a graufrette ftp instance based on configuration
 */
class FileReader
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

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
     * @param Filesystem $fileSystem
     */
    public function init(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
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
     * @param $files
     * @param $path
     * @param bool $single
     */
    public function download($files, $path, $single = false)
    {
        if ($single) {
            $this->downloadFile($files, $path);
        } else {
            foreach ($files as $file) {
                $this->downloadFile($file, $path);
            }
        }
    }

    /**
     * @param $file
     * @param $path
     */
    private function downloadFile($file, $path)
    {
        $content = $this->fileSystem->read($file);
        $handle = fopen("${path}/${file}", 'w+');

        fwrite($handle, $content);
        fclose($handle);
    }

    /**
     * @param string $filename
     * @param string $prefix
     * @return bool
     */
    public function prefixer($filename, $prefix)
    {
        if ($this->fileSystem->has($filename)) {
            return $this->fileSystem->rename($filename, $prefix.$filename);
        }

        return false;
    }
}
