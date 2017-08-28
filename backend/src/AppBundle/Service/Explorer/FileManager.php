<?php

namespace AppBundle\Service\Explorer;

use Symfony\Component\Finder\Finder;

class FileManager extends Finder implements FileManagerInterface
{
    private $dir;

    function __construct($dir = null, $create = true)
    {
        parent::__construct();

        if($dir){
            $this->setDir($dir, $create);
        }
        
        if($this->isReadable()){
            $this->in($this->dir);
        }
    }

    /**
     * @inheritDoc
     */
    public function setDir($dir, $create = true)
    {
        if(!is_dir($dir) && $create){
            mkdir($dir, 0777, true);
        }
        
        $this->dir = $dir;
        
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function files()
    {
        return parent::files()->in($this->dir);
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return is_readable($this->dir);
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        return is_writable($this->dir);
    }

    /**
     * @inheritDoc
     */
    public function remove()
    {
        if(is_dir($this->dir)){
            rmdir($this->dir);
        }

        return $this;
    }
}