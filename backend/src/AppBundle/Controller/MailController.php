<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Entity\EmailAccount;
use AppBundle\Entity\EmailAccountInterface;
use AppBundle\Form\EmailAccountType;
use AppBundle\Form\EmailComposeType;
use AppBundle\Form\ExplorerType;
use AppBundle\Service\FileExplorer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("mail")
 */
class MailController extends AbstractController
{
    /**
     * @Route("/i/{folder}", name="mail_inbox")
     */
    public function inboxAction(Request $request, $folder = null)
    {
        if(!$this->getCurrentMember()->getEmailAccounts()->count()){
            return $this->redirectToRoute('mail_account_manage');
        }

        $helper = $this->connect($folder);

        $pagination = $helper->paginateEmails(
            $request->query->getInt('page', 1),
            10,
            ['date', 'desc']
        );

        //$this->dd($pagination);

        return $this->render('mail.inbox', [
            'folder' => $folder,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/r/{id}/{folder}", name="mail_read")
     */
    public function readAction($id, $folder = null)
    {
        $helper = $this->connect($folder);
        
        $email = $helper->getEmail($id);
        
        return $this->render('mail.read', [
            'folder' => $folder,
            'email' => $email
        ]);
    }

    /**
     * @Route("/a/{id}/{file}/{folder}", name="mail_attachment")
     */
    public function attachmentAction(Request $request, $id, $folder = null, $file)
    {
        $helper = $this->connect($folder);

        $attachment = $helper->getAttachmentFileInfo($id, $file);

        //$this->dd($attachment);

        $response = new BinaryFileResponse($attachment->getRealPath());

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file);

        return $response;
    }

    /**
     * @Route("/folders", name="mail_folders")
     */
    public function foldersAction()
    {
        $folders = [];

        if(!$this->getCurrentMember()->getEmailAccounts()->isEmpty()) {
            $helper = $this->connect();
            $folders = $helper->getAccount()->getFolders();
        }

        return $this->render('mail.folders', [
            'folders' => $folders
        ]);
    }

    /**
     * @Route("/send", name="mail_send")
     */
    public function sendAction(Request $request)
    {
        $member = $this->getCurrentMember();

        if(!$member->getEmailAccounts()->count()){
            return $this->redirectToRoute('mail_account_manage');
        }

        $data = ['member' => $member];

        $form = $this->createForm(EmailComposeType::class, $data);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $email = $form->getData();

            $subject = $email['subject'];
            $toEmails = explode(';', trim($email['to']));
            $message = $email['message'];
            $attachments = json_decode($email['attachments']);

            if(!$attachments)
                $attachments = [];

            //$this->dd($attachments);

            /** @var FileExplorer $fileExplorer */
            $fileExplorer = $this->get('app.file_explorer');
            //$attachments = array_filter(explode('|', $email['attachments']));
            if(count($attachments)){
                foreach($attachments as $key => $attachment){

                    $attachment = $fileExplorer->loadById($attachment->id);

                    if($attachment) {
                        $attachments[$key] = $attachment['pathname'];
                    }else{
                        unset($attachment[$key]);
                    }
                }
            }

            $account = $this->getCurrentMember()->getCurrentEmailAccount();

            $helper = $this->getMailHelper();

            $helper->connect($account);
            
            $helper->sendMail($subject, $message, $toEmails, $attachments);

            return $this->redirectToRoute('mail_inbox');
        }

        return $this->render('mail.compose', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/explorer", name="mail_explorer")
     */
    public function explorerAction(Request $request)
    {
        $member = $this->getCurrentMember();

        $data = ['member' => $member];

        $form = $this->createForm(ExplorerType::class, $data);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            /** @var Customer $contact */
            $contact = $data['contact'];

            /** @var FileExplorer $explorer */
            $fileExplorer = $this->get('app.file_explorer');

            $files = $fileExplorer->fromContact($contact);

            return $this->render('mail.files', [
                'files' => $files
            ]);
        }

        return $this->render('mail.explorer', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/accounts", name="mail_accounts")
     */
    public function accountsAction(Request $request)
    {
        $accounts = $this->getCurrentMember()->getEmailAccounts();

        return $this->render('mail.accounts', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * @Route("/accounts/m/{token}", name="mail_account_manage", defaults={"token":null})
     */
    public function manageAccountAction(Request $request, EmailAccount $account = null)
    {
        $manager  = $this->getEmailAccountManager();

        if(!$account) {
            $account = $manager->create();
            $account->setOwner($this->getCurrentMember());
        }

        $form = $this->createForm(EmailAccountType::class, $account);

        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {

            if($this->checkDuplicateAccount($account, $error)){

                $helper = $this->getMailHelper();

                try {
                    $helper->connect($account);
                }catch (\Exception $e){
                    $error = $e->getMessage();
                }

                if(!$error) {

                    $subject = 'Mensagem de Teste';
                    $message = 'Este é um email enviado automaticamente durante o teste das configurações da sua conta.';

                    $helper->sendMail($subject, $message, [$account->getEmail()], [], true);

                    $this->processAndSaveAccount($account);

                    return $this->redirectToRoute('mail_accounts');
                }
            }
        }

        return $this->render('mail.account_form', [
            'form' => $form->createView(),
            'error' => $error,
            'defaults' => EmailAccount::getDefaultMails()
        ]);
    }


    /**
     * @return \AppBundle\Service\MailboxHelper
     */
    private function connect(&$folder = null)
    {
        $member = $this->getCurrentMember();
        
        $emailAccount = $member->getCurrentEmailAccount();
        $helper = $this->getMailHelper();

        $helper->connect($emailAccount);

        if($folder){

            $folder = $emailAccount->getFolder($folder);

            if($folder && !empty($folder)){

                $path = $helper->getRootPath().$folder['name'];
                $helper->getConnection()->switchMailbox($path);
            }
        }
        
        return $helper;
    }
    
    /**
     * @return \AppBundle\Service\MailboxHelper
     */
    private function getMailHelper()
    {
        return $this->get('app.mailbox');
    }

    /**
     * Reset current email account for member
     */
    private function processAndSaveAccount(EmailAccount &$account)
    {
        $manager = $this->getEmailAccountManager();

        if ($account->isCurrent()) {
            foreach ($this->getCurrentMember()->getEmailAccounts() as $emailAccount) {
                if ($account->getId() != $emailAccount->getId() && $emailAccount->isCurrent()) {
                    $emailAccount->setCurrent(false);
                    $manager->save($emailAccount, false);
                }
            }
        }

        $manager->save($account);
    }

    /**
     * @param EmailAccountInterface $account
     * @param $error
     */
    private function checkDuplicateAccount(EmailAccountInterface $account, &$error)
    {
        $manager = $this->getEmailAccountManager();

        if(!$account->getId()){
            $checkAccount = $manager->findOneBy(['email' => $account->getEmail()]);
            if($checkAccount){
                $error = 'email.errors.duplicate_email';
                return false;
            }
        }

        return true;
    }
}
