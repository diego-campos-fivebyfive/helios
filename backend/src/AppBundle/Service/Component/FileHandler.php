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
    private $bucketAmbience;

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
        $this->ambience = $container->getParameter('ambience');
        $this->bucketAmbience = ($this->ambience == 'production') ? 'production' : 'homolog';
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
     * @param $options
     * @param $file
     */
    public function push(array $options, $file)
    {
        $acl = ($options['access'] == 'public') ? 'public-read' : 'private';

        $this->s3->putObject([
            'Bucket' => "pss-{$this->bucketAmbience}-{$options['access']}",
            'Key' => "{$options['root']}/{$options['type']}/{$options['filename']}",
            'Body' => fopen($file, 'rb'),
            'ACL' => $acl
        ]);
    }

    /**
     * @param $options
     */
    public function link(array $options)
    {
        $host = 'https://s3-sa-east-1.amazonaws.com';
        $bucket = "pss-{$this->bucketAmbience}-{$options['access']}";
        $path = "{$host}/{$bucket}/{$options['root']}/{$options['type']}";

        return "{$path}/{$options['filename']}";
    }

    /**
     * @param $options
     */
    public function location(array $options)
    {
        $path = "{$this->container->get('kernel')->getRootDir()}/../..";
        return "{$path}/.uploads/{$options['root']}/{$options['type']}/{$options['filename']}";
    }

    /**
     * @param $options
     * @param $file
     */
    public function pdf(array $options, $file)
    {
        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('viewport-size', '1280x1024');
        $snappy->setOption('margin-top', 0);
        $snappy->setOption('margin-bottom', 0);
        $snappy->setOption('margin-left', 0);
        $snappy->setOption('margin-right', 0);
        $snappy->setOption('zoom', 2);

        $absoluteUrl = UrlGeneratorInterface::ABSOLUTE_URL;
        $snappyUrl = $this->generateUrl($options['doc'], ['id' => $options['id']], $absoluteUrl);

        $snappy->generate($snappyUrl, $file);
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
