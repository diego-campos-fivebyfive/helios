<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\UserInterface;
use AppBundle\Service\FileExplorer;
use AppBundle\Service\SessionStorage;
use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * UploadListener
 * Process Upload Events
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class UploadListener
{
    /**
     * @var SessionStorage
     */
    private $sessionStorage;
    /**
     * @var ContainerInterface
     */
    private $container;

    //function __construct(SessionStorage $sessionStorage)
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->sessionStorage = $this->container->get('app.session_storage');
    }

    public function onUpload(PostPersistEvent $event)
    {
        $response = $event->getResponse();
        $response['data'] = $this->sessionStorage->all();

        /*file_put_contents(
            $this->container->get('kernel')->getRootDir() . '/../storage/data.json',
            $response['data']
        //json_encode($uploadedFile->getClientOriginalName())
        );*/

        $this->handleUploadReference($event);
    }

    /**
     * @param PostPersistEvent $event
     */
    private function handleUploadReference(PostPersistEvent $event)
    {
        /** @var File $file */
        $file = $event->getFile();

        /** @var FileExplorer $fileExplorer */
        $fileExplorer = $this->container->get('app.file_explorer');

        //$file->move($targetDir, $file->getFilename());
        $fileExplorer->moveToContact($file);
    }
}