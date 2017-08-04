<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Component;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Util\Inflector;

/**
 * This class
 * Handle uploads by Symfony UploadedFile instances
 * Resolve component file reference
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class FileHandler
{
    /**
     * @var string
     */
    private $uploadDir;

    /**
     * FileHandler constructor.
     * @param $uploadDir
     */
    function __construct($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param \AppBundle\Entity\Component\StringBoxInterface $component
     * @param \AppBundle\Entity\Component\ModuleInterface $component
     * @param \AppBundle\Entity\Component\InverterInterface $component
     * @param \AppBundle\Entity\Component\StructureInterface $component
     * @param FileBag $files
     */
    public function upload($component, FileBag $files)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $name = self::name($component);
        $id = $component->getId();

        if(!$id){
            dump($component); die;
        }

        /**
         * @var string $field
         * @var UploadedFile $file
         */
        foreach ($files->all() as $field => $file) {

            if($file instanceof UploadedFile) {

                $extension = $file->getClientOriginalExtension();

                $format = 'pdf' == $extension ? '%s_%s.%s' : '%s_%s_thumb.%s';

                $filename = sprintf($format, $name, $id, $extension);

                $file->move($this->uploadDir, $filename);

                $accessor->setValue($component, $field, $filename);
            }
        }
    }

    /**
     * @param $component
     * @return string
     */
    private static function name($component)
    {
        $packs = explode('\\', get_class($component));

        return Inflector::tableize($packs[count($packs)-1]);
    }
}