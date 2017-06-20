<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Extra\EmailHandler;
use AppBundle\Entity\Project\Project;
use AppBundle\Form\Extra\ProposalMessageType;
use AppBundle\Model\Email\Message;
use AppBundle\Model\Email\ProposalMessage;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("email")
 */
class EmailController extends AbstractController
{
    /**
     * @Route("/{token}/proposal", name="email_proposal")
     */
    public function proposalAction(Request $request, Project $project)
    {
        $this->checkAccess($project);

        $member = $this->getCurrentMember();
        $account = $member->getAccount();
        $customer = $project->getCustomer();

        $message = new ProposalMessage();
        $message
            ->setSubject('Proposta via email')
            ->setFrom(['no-reply@inovadorsolar.com' => $member->getName()])
            ->setTo($customer->getEmail())
            ->setReplyTo([$member->getEmail() => $member->getName()])
        ;

        $form = $this->createForm(ProposalMessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $content = nl2br($message->getBody());

            $message->setBody($content);

            //$this->dd($message);

            // Redirect
            $url = $this->generateUrl('file_proposal',[
                'token' => $project->getToken()
            ]);

            $emailHandlerManager = $this->get('app.email_handler_manager');

            /** @var \AppBundle\Entity\Extra\EmailHandler $emailHandler */
            $emailHandler = $emailHandlerManager->create();

            $emailHandler
                ->setBehavior(EmailHandler::REDIRECT)
                ->setUrl($url);

            $emailHandlerManager->save($emailHandler);

            $parameters =  $this->getDocumentHelper()->loadFromAccount($account);

            $logo = $parameters->get('header_logo');

            $renderParams = [
                'isCopy' => false,
                'account' => $account,
                'message' => $message,
                'project' => $project,
                'handler' => $emailHandler,
                'logo' => $logo ? $logo->getFilename() : null
            ];

            $body = $this->render('financial.email_proposal', $renderParams)->getContent();

            $message->setBody($body);

            $swiftMessage = $message->swiftMessage();

            $mailer = $this->get('mailer');

            $mailer->send($swiftMessage);

            if($message->getSendCopy()){

                $message->setBody($content);

                $renderParams['message'] = $message;
                $renderParams['isCopy'] = true;

                $body = $this->render('financial.email_proposal', $renderParams)->getContent();

                $message
                    ->setReplyTo(null)
                    ->setBody($body)
                    ->setTo($member->getEmail());

                $swiftMessage = $message->swiftMessage();

                $mailer->send($swiftMessage);
            }

            $project->setMetadata('email', $project->getMetadata('email', 0));

            $projectManager = $this->getProjectManager();

            $projectManager->getObjectManager()->detach($parameters);

            $projectManager->save($project);

            return $this->jsonResponse([]);
        }

        return $this->render('email.proposal', [
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/", name="email")
     */
    public function formAction(Request $request)
    {
        /** @var \AppBundle\Entity\Project\Project $project */
        $project = $this->getProjectManager()->find(2);
        //dump($project); die;
        //$storageDir = $this->get('kernel')->getRootDir() . '/../storage/files/cjchamado@gmail.com/cjchamado@gmail.com/';
        //dump($baseDir); die;
        //$attachment = \Swift_Attachment::fromPath($storageDir . '0001.Dom.pdf');
        //dump($attachment); die;

        //$this->dd($project->getMetadata('filename'));

        $message = Message::newInstance();

        $member = $this->getCurrentMember();
        $customer = $project->getCustomer();

        $fromName = $member->getName();
        $fromEmail = $member->getEmail();

        $toName = $customer->getName();
        $toEmail = $customer->getEmail();

        $subject = 'Proposta 1111';

        $body = 'Hello World Big Data';

        $message
            ->setFrom($fromEmail, $fromName)
            ->setReplyTo($fromEmail, $fromName)
            ->setCc($fromEmail, $fromName)
            ->setTo($toEmail, $toName)
            ->setSubject($subject)
            ->setBody($body);

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->dd($message);
        }

        //$email->attach($attachment);
        //$this->dd($message->getTo());

        return $this->render('email.form', [
            'config' => [
                'toEmail' => $toEmail,
                'toName' => $toName,
                'subject' => $subject
            ],
            'project' => $project,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Project $project
     */
    private function checkAccess(Project $project)
    {
        $this->get('app.project_authorization')->isAuthorized($project);
    }
}