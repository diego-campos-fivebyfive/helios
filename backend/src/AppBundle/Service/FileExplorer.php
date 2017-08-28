<?php

namespace AppBundle\Service;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Project\Project;
use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\UserInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileExplorer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * FileExplorer constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $source
     */
    public function delete($source)
    {
        $file = $this->resolve($source);
        if($file instanceof \SplFileInfo && $file->isFile()){
            unlink($file->getRealPath());
        }
    }

    /**
     * @param $source
     * @return BinaryFileResponse
     */
    public function show($source)
    {
        return $this->serve($this->resolve($source), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    /**
     * @param $source
     * @return BinaryFileResponse
     */
    public function download($source)
    {
        return $this->serve($this->resolve($source), ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    /**
     * @param \SplFileInfo $file
     * @param $disposition
     * @param null $filename
     * @return BinaryFileResponse
     */
    public function serve(\SplFileInfo $file, $disposition, $filename = null)
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition($disposition, $filename ? $filename : $file->getFilename());

        return $response;
    }

    /**
     * @param $filename
     */
    public function moveTmpFile($file)
    {
        $file = $this->checkTmpFile($file);

        $file->move($this->getUserDir());

        return $file;
    }

    /**
     * @param BusinessInterface $contact
     * @return Finder
     */
    public function fromContact(BusinessInterface $contact)
    {
        $storage = $this->getContactStorage($contact);

        $finder = new Finder();

        $finder->in($storage)->files();

        $files = [];
        foreach ($finder as $file) {
            $files[] = $this->serialize($file);
        }

        $this->setReference($contact);

        return $files;
    }

    /**
     * @param $id
     * @return array|null
     */
    public function loadById($id)
    {
        list($path, $name) = explode('|', base64_decode($id));

        $finder = new Finder();
        $finder->in($path)->name($name);
        $file = null;

        if ($finder->count()) {
            foreach ($finder as $file) {
                $file = $this->serialize($file);
                break;
            }
        }

        return $file;
    }

    /**
     * @param File $file
     * @param BusinessInterface|null $contact
     */
    public function moveToContact(File $file, BusinessInterface $contact = null)
    {
        $contact = $this->getReference();

        if ($contact) {
            $storage = $this->getContactStorage($contact);
            $file->move($storage, $file->getFilename());
        }
    }

    /**
     * @return string
     */
    public function getRootStorage()
    {
        $config = $this->getUploaderConfig();
        return $config['mappings']['files']['storage']['directory'];
    }

    /**
     * @return BusinessInterface
     */
    private function getReference()
    {
        return $this->getSessionStorage()->get('file_explorer_reference');
    }

    /**
     * @param BusinessInterface $contact
     */
    private function setReference(BusinessInterface $contact)
    {
        $this->getSessionStorage()->set('file_explorer_reference', $contact);
    }

    private function getContactStorage(BusinessInterface $contact)
    {
        $account = $contact->getMember()->getAccount();

        $config = $this->getUploaderConfig();

        $baseDir = $config['mappings']['files']['storage']['directory'];

        $storage = sprintf('%s%s/%s/', $baseDir, $account->getId(), $contact->getId());

        if (!is_dir($storage))
            mkdir($storage, 0777, true);

        return $storage;
    }

    /**
     * @param \SplFileInfo $file = file or dir
     * @return array
     */
    private function serialize(\SplFileInfo $file)
    {
        $baseId = $file->isDir() ? $file->getPathname() : $file->getPath() . '|' . $file->getFilename();

        return [
            'id' => base64_encode($baseId),
            'path' => $file->getPath(),
            'pathname' => $file->getPathname(),
            'filename' => $file->getFilename(),
            'type' => $file->getType(),
            'aTime' => $file->getATime(),
            'mTime' => $file->getMTime(),
            'cTime' => $file->getCTime(),
            'size' => $file->getSize()
        ];
    }

    /**
     * @return array
     */
    private function getUploaderConfig()
    {
        return $this->container->getParameter('oneup_uploader.config');
    }

    /**
     * @return SessionStorage
     */
    private function getSessionStorage()
    {
        return $this->container->get('app.session_storage');
    }

    /**
     * @return string
     */
    private function getUserDir(UserInterface $user = null)
    {
        if (!$user) {
            $user = $this->getUser();
        }

        $member = $user->getInfo();
        $account = $member->getAccount();

        return $this->checkDir(sprintf('%s/%s/%s/', $this->getFilesDir(), $account->getEmail(), $user->getEmail()));
    }

    /**
     * @return string
     */
    public function getTmpDir($filename = null)
    {
        $pathname = $this->checkDir($this->getStorageDir() . '/temp');
        if ($filename) {
            return $pathname . self::ds() . $filename;
        }

        return $pathname;
    }

    /**
     * @param ProjectInterface $project
     * @return File
     */
    private function getProjectFile(ProjectInterface $project)
    {
        $this->getProjectAuthorization()->isAuthorized($project);

        $member = $project->getMember();
        $user = $member->getUser();

        $pathname = $this->getUserDir($user) . $project->getMetadata('filename');

        return new File($pathname, false);
    }

    /**
     * @return string
     */
    private function getFilesDir()
    {
        return $this->checkDir($this->getStorageDir() . '/files');
    }

    /**
     * @return string
     */
    private function getStorageDir()
    {
        return $this->checkDir($this->container->get('kernel')->getRootDir() . '/../storage');
    }

    /**
     * Check if directory exists, if not, create directory
     *
     * @param $pathname
     * @param int $mode
     * @param bool $recursive
     * @return string
     */
    private function checkDir($pathname, $mode = 0777, $recursive = true)
    {
        if (!is_dir($pathname)) {
            mkdir($pathname, $mode, $recursive);
        }

        return $pathname;
    }

    /**
     * @param $file
     * @return string|File
     */
    private function checkUserFile($file)
    {
        if (!$file instanceof File) {

            $path = $file;

            if (!file_exists($path)) {
                $path = $this->getUserDir() . $file;
            }

            $file = new File($path);
        }

        return $file;
    }

    /**
     * @param $file
     * @return File
     */
    private function checkTmpFile($file)
    {
        if (!$file instanceof File) {
            $path = $file;
            if (!file_exists($path)) {
                $path = $this->getTmpDir() . self::ds() . $path;
            }
            $file = new File($path);
        }

        return $file;
    }

    /**
     * @param $source
     * @return File
     */
    private function resolve($source)
    {
        if($source instanceof ProjectInterface){
            $file = $this->getProjectFile($source);
        }else {
            $file = $this->checkUserFile($source);
        }

        return $file;
    }

    /**
     * @return BusinessInterface
     */
    private function getMember()
    {
        return $this->getUser()->getInfo();
    }

    /**
     * @return UserInterface
     */
    private function getUser()
    {
        /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->container->get('security.token_storage');
        return $tokenStorage->getToken()->getUser();
    }

    /**
     * @return string
     */
    private static function ds()
    {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * @return \AppBundle\Service\Security\ProjectAuthorization
     */
    private function getProjectAuthorization()
    {
        return $this->container->get('app.project_authorization');
    }
}