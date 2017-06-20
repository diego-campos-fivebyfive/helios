<?php

namespace AppBundle\Service;

use AppBundle\Entity\EmailAccount;
use AppBundle\Entity\EmailAccountInterface;
use AppBundle\Entity\EmailAccountManagerInterface;
use AppBundle\Service\PhpImap\Credential;
use AppBundle\Service\PhpImap\CredentialInterface;
use AppBundle\Service\PhpImap\MailboxFactory;
use AppBundle\Service\PhpImap\Path;
use AppBundle\Service\PhpImap\PathInterface;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Finder\Finder;

class MailboxHelper
{

    /**
     * @var EmailAccountInterface
     */
    private $account;

    /**
     * @var Mailbox
     */
    private $connection;

    /**
     * @var bool
     */
    private $validateCert = false;

    /**
     * @var string
     */
    private $storageDir;

    /**
     * @var EmailAccountManagerInterface
     */
    private $accountManager;

    function __construct(EmailAccountManagerInterface $accountManager, $storageDir)
    {
        if (!is_dir($storageDir)) {
            throw new \InvalidArgumentException(sprintf('The directory %s does not exist', $this->storageDir));
        }

        $this->storageDir = $storageDir;
        $this->accountManager = $accountManager;
    }

    /**
     * @param null $criteria
     * @param int $page
     * @param int $num
     * @return mixed
     */
    public function paginateEmails($page = 1, $perPage = 10, $sort = null)
    {
        $sortEmails = $this->connection->sortMails();
        $countEmails = count($sortEmails);

        $offset = 1 == $page ? 0 : ($perPage * ($page - 1));
        $limit = $perPage;

        $sliceEmails = array_slice($sortEmails, $offset, $limit);
        $emails = array_reverse($this->connection->getMailsInfo($sliceEmails));

        $start = $page > 1 ? $perPage * ($page - 1) + 1 : 1;
        $end = $page > 1 ? $start + ($perPage - 1) : $perPage;

        return [
            'total' => $countEmails,
            'page' => $page,
            'num_per_page' => $perPage,
            'pages' => ceil($countEmails / $perPage),
            'range' => ['start' => $start, 'end' => $end],
            'emails' => $emails
        ];
    }

    public function getFolders()
    {
        $path = str_replace('INBOX', '', $this->connection->checkMailbox()->Mailbox);
        $this->connection->switchMailbox($path);
        return $this->connection->getListingFolders();
    }

    public function getMailbox()
    {
        return $this->connection->checkMailbox()->Mailbox;
    }

    public function getRootPath()
    {
        $mailbox = $this->getMailbox();
        return substr($mailbox, 0, strrpos($mailbox, '}')+1);
    }

    public function isConnected()
    {
        return $this->connection->checkMailbox();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return EmailAccountInterface
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return \stdClass
     */
    public function getStatus()
    {
        return $this->connection->statusMailbox();
    }

    /**
     * @param $id
     * @return \PhpImap\IncomingMail
     */
    public function getEmail($id)
    {
        $email = $this->connection->getMail($id);

        if(!$email->textHtml && !$email->textPlain){
            $attachments = $email->getAttachments();
            if(!empty($attachments)){
                $ext = '.plain';
                foreach($attachments as $key => $attachment){

                    $name = $attachment->name;

                    if(strrpos($name, $ext) == strlen($name)-strlen($ext)){
                        $file = $this->getAttachmentFileInfo($id, $name);
                        if($file instanceof \SplFileInfo) {
                            //$crawler = new Crawler(file_get_contents($file->getRealPath()));
                            $email->textHtml = (file_get_contents($file->getRealPath()));
                            unset($attachments[$key]);
                        }
                    }
                }
            }
        }

        return $email;
    }

    public function getAttachments($mail, $attachment)
    {
        if (!$this->account)
            $this->createException('Account is not connected');

        if (!is_numeric($mail))
            $mail = $this->decodeId($mail);

        $attachmentDir = sprintf('%s/%s/%s/', $this->storageDir, $this->account->getEmail(), $mail);

        if (!is_dir($attachmentDir)) {
            mkdir($attachmentDir, 0777, true);
        }

        $finder = new Finder();

        $finder->in($attachmentDir)->files();

        if (!$finder->count()) {

            $this->connection->setAttachmentsDir($attachmentDir);
            $this->getEmail($mail)->getAttachments();
        }

        foreach ($finder as $file) {
            return $file; break;
        }
    }

    /**
     * @param $email
     * @param $attachment
     * @return \SplFileInfo
     */
    public function getAttachmentFileInfo($email, $attachment)
    {
        return $this->getAttachments($email, $attachment);
    }

    /**
     * @return bool
     */
    public function check()
    {
        return $this->connection->createMailbox();
    }

    /**
     * @return null|resource
     */
    public function getStream()
    {
        return $this->connection->getImapStream();
    }

    /**
     * @param $id
     */
    public function saveEmail($id)
    {
        $this->connection->saveMail($id);
        return $this;
    }

    /**
     * @param $id
     */
    public function markAsRead($id)
    {

        if (!is_numeric($id))
            $id = $this->decodeId($id);

        $this->connection->markMailAsRead($id);
        return $this;
    }

    /**
     * @param $id
     */
    public function deleteEmail($id)
    {

        if (!is_numeric($id))
            $id = $this->decodeId($id);

        $this->connection->deleteMail($id);
        return $this;
    }

    /**
     * @param $id
     */
    public function markAsImportant($id)
    {

        if (!is_numeric($id))
            $id = $this->decodeId($id);

        $this->connection->markMailAsImportant($id);
        return $this;
    }

    /**
     * @param string $criteria
     * @return array
     */
    public function getEmails($criteria = 'ALL')
    {
        $emails = [];
        foreach ($this->connection->searchMailbox($criteria) as $id)
        {
            $emails[] = $this->connection->getMail($id);
        }
        return $emails;
    }

    /**
     * @return int
     */
    public function countEmails()
    {
        return $this->connection->countMails();
    }

    /**
     * @param EmailAccountInterface $account
     * @return $this
     */
    public function connect(EmailAccountInterface $account)
    {
        $this->connection = $this->connectAccount($account);

        if(!$account->hasFolders()){
            $this->synchronizeFolders($account);
            $this->accountManager->save($account);
        }

        return $this;
    }

    /**
     * @param $overview
     */
    private function sanitizeOverview(&$overview)
    {
        $overview->subject = str_replace('_', ' ', mb_decode_mimeheader($overview->subject));
        $overview->from = str_replace('_', ' ', mb_decode_mimeheader($overview->from));
        $overview->date = date('d/m/Y H:i:s', strtotime($overview->date));
        $overview->hash_id = $this->encodeId($overview->uid);
    }

    /**
     * @param $validateCert
     */
    public function validateCert($validateCert)
    {
        $this->validateCert = (bool) $validateCert;
    }

    /**
     * @param $id
     * @return string
     */
    public function encodeId($id)
    {
        return base64_encode($id);
    }

    /**
     * @param $id
     * @return int
     */
    public function decodeId($id)
    {
        $decode = (int) base64_decode($id);

        if (!is_int($decode))
            throw new \InvalidArgumentException('Invalid email id');

        return $decode;
    }

    /**
     * @param $subject
     * @param $message
     * @param array $toEmails
     * @param array $attachments
     */
    public function sendMail($subject, $message, array $toEmails, array $attachments = [], $testing = false)
    {
        $sendViaImap = true;
        $sendViaSmtp = false;

        $account = $this->account;

        $transport = \Swift_SmtpTransport::newInstance(
            $account->getOutputServer(), $account->getOutputPort()
        )->setUsername($account->getEmail())->setPassword($account->getPassword());

        $mailer = \Swift_Mailer::newInstance($transport);

        /** @var \Swift_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->account->getEmail())
            ->setTo($toEmails)
            ->setBody($message)
            ->setContentType('text/html')
        ;

        if(!empty($attachments)){
            foreach($attachments as $attachment){
                $message->attach(\Swift_Attachment::fromPath($attachment));
            }
        }

        $messageString = $message->toString();

        //var_dump($messageString); die;

        /**
         * Send options
         * 1 - Via IMAP
         * 2 - Via SMTP
         */
        if($sendViaImap) {
            foreach ($message->getTo() as $toEmail => $toName) {
                imap_mail($toEmail, $message->getSubject(), $messageString, $message->getHeaders());
            }
        }elseif($sendViaSmtp){
            $mailer->send($message);
        }

        $sent = $testing ? 'Inbox' : ('imap' == $account->getType() ? $this->detectSentMailbox() : 'Sent');

        // TODO: Test connection
        // TODO: this method get only INBOX mailbox
        $stream = $this->connection->getImapStream();
        $mailbox = $this->getRootPath().$sent;

        //dump($mailbox); die;
        //$append = imap_append($stream, $mailbox, $messageString); //or die(imap_last_error());
    }

    /**
     * @param EmailAccountInterface $account
     * @return \PhpImap\Mailbox
     */
    private function connectAccount(EmailAccountInterface $account)
    {
        $path = $this->createPath($account);
        $credential = $this->createCredential($account);

        $this->account = $account;

        $connection = MailboxFactory::create($path, $credential);

        $check = $connection->checkMailbox();

        if(!$check){
            throw new \Exception(imap_last_error());
        }

        $connection->switchMailbox($check->Mailbox);

        return $connection;
    }

    /**
     * @param EmailAccountInterface $account
     * @return PathInterface
     */
    private function createPath(EmailAccountInterface $account)
    {
        $extras = [];

        $extras[] = $account->getType();

        switch ($account->getInputEncryption())
            {
            case EmailAccountInterface::ENCRYPTION_TLS:
            case EmailAccountInterface::ENCRYPTION_SSL:
                $extras[] = $account->getInputEncryption();
                break;
            }

        if (!$this->validateCert)
            $extras[] = 'novalidate-cert';

        return new Path($account->getInputServer(), $account->getInputPort(), $extras);
    }

    /**
     * @param EmailAccountInterface $account
     * @return CredentialInterface
     */
    private function createCredential(EmailAccountInterface $account)
    {
        return new Credential($account->getEmail(), $account->getPassword());
    }

    private function synchronizeFolders(EmailAccountInterface &$account)
    {
        $this->connection->switchMailbox($this->getRootPath());

        $folders = $this->connection->getListingFolders();

        $accountFolders = [];

        if($folders && !empty($folders)){

            foreach($folders as $folder){

                $accountFolders[md5($folder)] = [
                    'id' => md5($folder),
                    'name' => $folder,
                    'enabled' => true
                ];
            }
        }

        $account->setFolders($accountFolders);
    }

    private function detectSentMailbox($testing = false)
    {
        $domain = $this->account->getEmailDomain();

        if($domain == 'gmail.com'){
            return '[Gmail]/E-mails enviados';
        }

        return 'Sent';
    }

    /**
     * @param $message
     * @throws \Exception
     */
    private function createException($message)
    {
        throw new \Exception($message);
    }

}
