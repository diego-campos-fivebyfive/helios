<?php

namespace AppBundle\Service\Security;

use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class ProjectAuthorization
 * @package AppBundle\Service\Security
 */
class ProjectAuthorization
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var UserInterface
     */
    private $user;
    
    /**
     * ProjectAuthorization constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     */
    function __construct(TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->getUser();
    }

    /**
     * @param ProjectInterface $target
     */
    public function isAuthorized($target)
    {
        if($target instanceof ProjectInterface)
            return $this->checkAuthorizationProject($target);

        return true;
    }

    /**
     * @param ProjectInterface $project
     * @return bool
     */
    private function checkAuthorizationProject(ProjectInterface $project)
    {
        $member = $this->getMember();
        $memberToken = $member->getToken();

        $projectToken = $project->getToken();

        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        $authorizations = $session->get('authorizations', ['projects' => []]);

        $projects = $authorizations['projects'];

        if(array_key_exists($projectToken, $projects)){
            if(array_key_exists($memberToken, $projects[$projectToken])){

                $config = $projects[$projectToken][$memberToken];

                if('deny' == $config){
                    $this->denyAccess();
                }

                return true;
            }
        }

        $member = $this->getMember();

        if($member->getId() != $project->getMember()->getId()){
            if(!$member->isOwner()
            || ($member->getAccount()->getId() != $project->getMember()->getAccount()->getId())){

                $authorizations['projects'][$projectToken][$memberToken] = 'deny';
                $session->set('authorizations', $authorizations);

                $this->denyAccess();
            }
        }

        $authorizations['projects'][$projectToken][$memberToken] = [];
        $session->set('authorizations', $authorizations);

        return true;
    }

    /**
     * @return \AppBundle\Entity\BusinessInterface|null
     */
    private function getMember()
    {
        return $this->user->getInfo();
    }

    /**
     * @return \AppBundle\Entity\UserInterface
     */
    private function getUser()
    {
        $this->user = $this->tokenStorage->getToken()->getUser();

        if(!$this->user || 'anon.' == $this->user)
            $this->denyAccess();
    }

    /**
     * Common Deny Access
     */
    private function denyAccess()
    {
        throw new AccessDeniedException();
    }
}