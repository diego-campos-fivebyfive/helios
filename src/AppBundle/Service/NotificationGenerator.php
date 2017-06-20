<?php

namespace AppBundle\Service;

use AppBundle\Configuration\App;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Notification;
use AppBundle\Entity\NotificationManager;
use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\TaskInterface;

class NotificationGenerator
{
    /**
     * @var NotificationManager
     */
    private $manager;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * NotificationGenerator constructor.
     * @param NotificationManager $manager
     * @param \Twig_Environment $twig
     */
    function __construct(NotificationManager $manager, \Twig_Environment $twig)
    {
        $this->manager = $manager;
        $this->twig = $twig;
    }

    /**
     * @param TaskInterface $task
     * @return \AppBundle\Entity\NotificationInterface
     */
    public function scheduledTask(TaskInterface $task)
    {
        $members = $task->getMembers()->toArray();

        $notification = $this->manager->generate(
            'timeline',
            App::icons('tasks'),
            'Scheduled task',
            $task->getDescription(),
            $members
        );

        return $notification;
    }

    /**
     * @param ProjectInterface $project
     * @return \AppBundle\Entity\NotificationInterface
     */
    public function proposalIssued(ProjectInterface $project)
    {
        if(!$project->hasProposal()){
            throw new \InvalidArgumentException('This project has not proposal');
        }

        $content = $this->render('proposal_issued', [
            'type' => 'timeline',
            'project' => $project
        ]);

        $members = $this->createMemberSubscriberList($project);

        $notification = $this->manager->generate(
            'timeline',
            App::icons('proposal'),
            'Proposal issued',
            $content,
            $members
        );

        return $notification;
    }


    public function createdContact(BusinessInterface $contact)
    {
        if(!$contact->isContact()){
            throw new \InvalidArgumentException('Unsupported contact context');
        }

        $icon = $contact->isPerson() ? App::icons('person') : App::icons('company') ;

        $content = $this->render('contact_created', [
            'contact' => $contact
        ]);

        $members = $this->createMemberSubscriberList($contact);

        $notification = $this->manager->generate(
            Notification::TYPE_TIMELINE,
            $icon,
            'Added contact',
            $content,
            $members
        );

        return $notification;
    }

    /**
     * @param BusinessInterface $member
     * @return array
     */
    private function createMemberSubscriberList($source)
    {
        if(!method_exists($source, 'getMember')){
            throw new \InvalidArgumentException('Method "getMember" not found');
        }

        /** @var BusinessInterface $member */
        $member = $source->getMember();
        $account = $member->getAccount();
        $owners = $account->getOwners()->toArray();

        $members = [$member];

        if($source instanceof ProjectInterface){
            if(!$member->isOwner()){
                $members = array_merge($owners, $members);
            }
        }

        if($source instanceof BusinessInterface && $source->isContact()){
            if(!$member->isOwner()){
                $members = array_merge($owners, $members);
            }

            if(null != $accessors = $source->getAccessors()){
                $members = array_merge($accessors->toArray(), $members);
            }
        }

        return $members;
    }

    /**
     * @param $view
     * @param array $data
     * @return string
     */
    private function render($view, array $data = [])
    {
        return $this->twig->render(sprintf('AppBundle:Notification:%s.html.twig', $view), $data);
    }
}