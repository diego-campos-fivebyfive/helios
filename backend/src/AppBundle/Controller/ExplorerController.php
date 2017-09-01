<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Form\ExplorerType;
use AppBundle\Service\FileExplorer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("explorer")
 */
class ExplorerController extends AbstractController
{
    /**
     * @Route("/uploader", name="explorer_uploader")
     */
    public function uploaderAction(Request $request)
    {
        $file = null;

        if($request->isMethod('post')){

            $uploadedFile = $request->files->get('explorer_file');

            if($uploadedFile instanceof UploadedFile){

                $dir = $this->get('kernel')->getRootDir() . '/../web/uploads/user-files/';
                $filename = md5(uniqid(time())) . '.' . $uploadedFile->getClientOriginalExtension();

                $file = $uploadedFile->move($dir, $filename);
            }
        }

        return $this->render('explorer.uploader', [
            'file' => $file
        ]);
    }

    /**
     * @Route("/contact", name="contact_files")
     */
    public function contactFilesAction(Request $request)
    {
        $member = $this->getCurrentMember();

        $data = ['member' => $member];

        $form = $this->createForm(ExplorerType::class, $data);

        $form->handleRequest($request);

        $finder = null;

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            $contact = $data['contact'];

            $storage = $this->getStorage($contact);

            $finder = new Finder();
            $finder->in($storage)->files();

            /*foreach($finder as $file){
                dump($file); die;
            }
            die;*/
        }


        return $this->render('explorer.files', [
            'finder' => $finder,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/directories/{root}", name="explorer_directories")
     */
    public function directoriesAction($root = null)
    {
        $storage = $root ? base64_decode($root) : $this->getStorage();
        
        $directories = $this->discovery($storage);


        return $this->jsonResponse([
            'directories' => $directories
        ]);
    }


    /**
     * @Route("/", name="files_index")
     */
    public function indexAction()
    {
        $member = $this->getCurrentMember();
        $account = $member->getAccount();

        $config = $this->getParameter('oneup_uploader.config');

        $baseDir = $config['mappings']['files']['storage']['directory'];

        $storage = sprintf('%s%s/%s/', $baseDir, $account->getId(), $member->getId());

        if(!is_dir($storage))
            mkdir($storage, 0777, true);

        $finder = new Finder();

        $finder->files()->in($storage);

        $files = [];
        foreach($finder as $file){
            if($file instanceof \SplFileInfo){
                $files[] = $this->serialize($file);
            }
        }

        return $this->jsonResponse([
            'files' => $files
        ]);
    }

    /**
     * @Route("/debug", name="email_debug")
     */
    public function debugAction()
    {
        // TODO: Remove in production
        
        $contact = $this->manager('customer')->find(151);

        /** @var FileExplorer $explorer */
        $explorer = $this->get('app.file_explorer');

        $files = $explorer->fromContact($contact);

        $this->dd($files);
    }

    /**
     * @Route("/{id}/show", name="files_show")
     */
    public function showAction($id)
    {
        list($path, $name) = explode('|', base64_decode($id));

        $finder = new Finder();
        $finder->in($path)->name($name);
        $file = null;

        if($finder->count()) {
           foreach($finder as $file){
               $file = $this->serialize($file); break;
           }
        }

        return $this->jsonResponse([
            'file' => json_encode($file)
        ]);
    }

    /**
     * @param \SplFileInfo $file = file or dir
     * @return array
     */
    private function serialize(\SplFileInfo $file)
    {
        $baseId = $file->isDir() ? $file->getPathname() : $file->getPath() . '|' . $file->getFilename() ;

        return [
            'id' => base64_encode($baseId),
            'path' => $file->getPath(),
            'pathname' => $file->getPathname(),
            'filename' => $file->getFilename(),
            'type' => $file->getType()
        ];
    }

    /**
     * @return string
     */
    private function getStorage(Customer $contact)
    {
        $this->checkAccess($contact);

        $account = $this->getCurrentAccount();

        $config = $this->getParameter('oneup_uploader.config');

        $baseDir = $config['mappings']['files']['storage']['directory'];

        $storage = sprintf('%s%s/%s/', $baseDir, $account->getId(), $contact->getId());

        if(!is_dir($storage))
            mkdir($storage, 0777, true);

        return $storage;
    }

    private function discovery($root)
    {
        $finder = new Finder();

        $finder->directories()->in($root)->depth(0);

        $directories = [];
        if($finder->count()) {
            foreach ($finder as $dir) {
                if($dir instanceof \SplFileInfo) {
                    $info = $this->serialize($dir);

                    $children = [];

                    $fileFinder = new Finder();
                    $fileFinder->in($dir->getPathname())->depth(0)->files();

                    if($fileFinder->count()){
                        foreach($fileFinder as $file){
                            $children[] = $this->serialize($file);
                        }
                    }

                    $children = array_merge($children, $this->discovery($dir->getPathname()));

                    $info['children'] = $children;

                    $directories[] = $info;
                }
            }
        }

        return $directories;
    }

    /**
     * @param Customer $contact
     */
    private function checkAccess(Customer $contact)
    {
        if(!$this->getCurrentMember()->getAllowedContacts()->contains($contact)){
            throw $this->createAccessDeniedException();
        }
    }
}
