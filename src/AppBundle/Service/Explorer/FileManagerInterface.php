<?php

namespace AppBundle\Service\Explorer;

interface FileManagerInterface
{
    function __construct($dir = null, $create = true);

    /**
     * @param $dir
     * @param bool $create
     * @return FileManagerInterface
     */
    public function setDir($dir, $create = true);

    /**
     * @return bool
     */
    public function isReadable();

    /**
     * @return bool
     */
    public function isWritable();

    /**
     * @return mixed
     */
    public function files();

    /**
     * @return FileManagerInterface
     */
    public function remove();
}