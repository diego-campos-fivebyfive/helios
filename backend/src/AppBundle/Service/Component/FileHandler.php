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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Util\Inflector;
use Aws\S3\S3Client;

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
    private $s3;

    /**
     * @var string
     */
    private $ambience;

    /**
     * @var string
     */
    private $container;


    /**
     * FileHandler constructor.
     * @param $uploadDir
     */
    function __construct(ContainerInterface $container)
    {
        $this->s3 = $container->get('aws.s3');
        $this->ambience = $container->getParameter('ambience') == 'production' ? 'production' : 'homolog';
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

        if(!$component->getId()){
            throw new \InvalidArgumentException('Only persistent entities can be handled!');
        }

        /**
         * @var string $field
         * @var UploadedFile $file
         */
        foreach ($files->all() as $field => $file) {

            if($file instanceof UploadedFile) {

                $extension = $file->getClientOriginalExtension();

                if ('pdf' == $extension) {
                    $format = '%s_%s.%s';
                    $type = 'datasheet';
                }
                else {
                    $format = '%s_%s_thumb.%s';
                    $type = 'image';
                }

                $filename = sprintf($format, $name, $component->getId(), $extension);

                $this->move([
                    'file' => $file,
                    'filename' => $filename,
                    'root' => 'component',
                    'type' => $type,
                    'access' => 'public'
                ]);

                $accessor->setValue($component, $field, $filename);
            }
        }
    }

    /**
     * @param $config
     */
    public function move(array $config)
    {
        $acl = ($config['access'] == 'public') ? 'public-read' : 'private';

        $this->s3->putObject([
            'Bucket' => "pss-{$this->ambience}-{$config['access']}",
            'Key' => "{$config['root']}/{$config['type']}/{$config['filename']}",
            'Body' => fopen($config['file'], 'rb'),
            'ACL' => $acl
        ]);
    }

    /**
     * @param $config
     */
    public function link(array $config)
    {
        $host = 'https://s3-sa-east-1.amazonaws.com';
        $bucket = "pss-{$this->ambience}-{$config['access']}";
        $path = "{$host}/{$bucket}/{$config['root']}/{$config['type']}";

        return "{$path}/{$config['filename']}";
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
