<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Project\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Route("files")
 */
class FileController extends AbstractController
{
    /**
     * @Route("/{token}/proposal", name="file_proposal")
     */
    public function proposalAction(Project $project)
    {
        $member = $project->getMember();
        $account = $member->getAccount();

        $token = md5(uniqid(time()));

        $file = $this->get('kernel')->getRootDir() . sprintf(
                '/../storage/files/%s/%s/%s',
                $account->getEmail(),
                $member->getEmail(),
                $project->getMetadata('filename')
            );

        $files = $this->getStore('proposal_files');

        $files[$token] = base64_encode($file);

        $this->store('proposal_files', $files);

        $tag = sprintf('Proposta - %s', $project->getNumber());

        $project->setMetadata('email', ($project->getMetadata('email', 0) + 1));
        $this->getProjectManager()->save($project);

        return $this->redirectToRoute('files_download', [
            'token' => $token,
            'tag' => $tag
        ]);
    }

    /**
     * @Route("/{token}/v/{tag}", name="files_download", defaults={"tag":"Visualizar Arquivo"})
     */
    public function downloadOrViewAction($token)
    {
        $files = $this->getStore('proposal_files');

        if(array_key_exists($token, $files)){

            $file = base64_decode($files[$token]);

            if(file_exists($file)){
                return new BinaryFileResponse($file);
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * @param $id
     * @return array
     */
    private function getStore($id)
    {
        return $this->restore($id, [], false);
    }
}