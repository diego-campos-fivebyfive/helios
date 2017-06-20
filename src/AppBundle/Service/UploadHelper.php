<?php

namespace AppBundle\Service;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Generate a custom name and storage name in session for use after upload
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class UploadHelper implements NamerInterface
{
    const SESSION_KEY = 'app_upload_name';

    private $sessionStorage = 'app.session_storage';

    /**
     * @var ContainerInterface
     */
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function name(FileInterface $file)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getMasterRequest();
        $module = $request->request->get('module');

        if(!$module) {

            $files = $request->files->all();
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $files['file'][0];

            /* file_put_contents(
                 $this->container->get('kernel')->getRootDir() . '/../storage/data.json',
                 $files
                 //json_encode($uploadedFile->getClientOriginalName())
             );*/

            if (!$module && $uploadedFile instanceof UploadedFile) {
                return $uploadedFile->getClientOriginalName();
            }
        }

        /**
         * @deprecated
         * Soon this mechanism will be moved to the personalized treatment up
         */
        $name = md5(uniqid(time())) . '.' . $file->getExtension();
        $storage = $this->getSessionStorage();
        $storage->set($module, $name);

        return $name;
    }
    
    /**
     * @param $filename
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function createMedia($filename, $context)
    {
        $provider = 'sonata.media.provider.image';

        /** @var \Sonata\MediaBundle\Filesystem\Local $filesystem */
        $filesystem = $this->container->get('sonata.media.adapter.filesystem.local');

        //$uploadDir = $this->container->get('kernel')->getRootDir() . '/../web/uploads/';
        $uploadDir = $filesystem->getDirectory() . '/../';

        $file = $uploadDir . $filename;

        $mediaManager = $this->getMediaManager();

        $media = $mediaManager->create();

        if (file_exists($file)) {

            $media->setContext($context);
            $media->setBinaryContent($file);
            $media->setProviderName($provider);
            $media->setProviderReference($file);

            $mediaManager->save($media);

            if($media->getId())
                unlink($file);

        }

        return $media;
    }

    /**
     * @param $id
     * @return object
     */
    public function getMediaProvider($id)
    {
        return $this->container->get($id);
    }

    /**
     * @return \Sonata\MediaBundle\Entity\MediaManager
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
    }

    /**
     * @return SessionStorage
     */
    private function getSessionStorage()
    {
        return $this->container->get($this->sessionStorage);
    }
}