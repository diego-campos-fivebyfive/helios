<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Twig\Resolver;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Configuration\App;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

abstract class AbstractController extends Controller
{

    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     * @return Response
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $this->normalizeView($view);

        return parent::render($view, $parameters, $response);
    }

    /**
     * @inheritDoc
     */
    public function renderView($view, array $parameters = array())
    {
        $this->normalizeView($view);

        return parent::renderView($view, $parameters);
    }

    /**
     * @deprecated use self::json()
     *
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function jsonResponse(array $data = [], $status = 200, array $headers = [])
    {
        return $this->json($data, $status, $headers);
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function json(array $data = [], $status = 200, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param $string
     * @param array $parameters
     * @param null $domain
     * @param null $locale
     * @return string
     */
    protected function translate($string, array $parameters = [], $domain = null, $locale = null)
    {
        /** @var \Symfony\Component\Translation\DataCollectorTranslator $translator */
        $translator = $this->get('translator');
        
        return $translator->trans($string, $parameters, $domain, $locale);
    }

    /**
     * @param $view
     */
    protected function normalizeView(&$view)
    {
        Resolver::resolveView($view);
    }

    /**
     * @param $view
     */
    protected function clearTemplateCache($view)
    {
        $this->normalizeView($view);

        if('prod' == $this->get('kernel')->getEnvironment()) {

            $cache = $this->get('twig')->getCacheFilename($view);

            if (is_file($cache))
                unlink($cache);
        }
    }

    /**
     * @return bool
     */
    protected function isXmlHttpRequest()
    {
        return $this->getCurrentRequest()->isXmlHttpRequest();
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected function getSession()
    {
        $session = $this->getCurrentRequest()->getSession();

        if (!$session->isStarted())
            $session->start();

        return $session;
    }

    /**
     * @param $message
     */
    protected function setNotice($message, $type = 'success', $title = '')
    {
        $this->getSession()->getFlashBag()->add('notice', [
            'type' => $type,
            'message' => $message,
            'title' => $title
        ]);
    }

    /**
     * @param $key
     * @param $value
     */
    protected function store($key, $value)
    {
        $this->getSession()->set($key, $value);
    }

    /**
     * @param $key
     * @param null $default
     * @param bool $reset
     * @return mixed
     */
    protected function restore($key, $default = null, $reset = true)
    {
        $session = $this->getSession();
        $value = $session->get($key, $default);

        if($reset){
            $session->remove($key);
        }

        return $value;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getCurrentRequest()
    {
        $requestStack = $this->get('request_stack');

        if (!$requestStack instanceof RequestStack)
            throw $this->createNotFoundException('Request stack no running');

        return $requestStack->getCurrentRequest();
    }

    /**
     * @return \Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
     */
    protected function getAclProvider()
    {
        return $this->get('security.acl.provider');
    }

    /**
     * @param $object
     * @return ObjectIdentityInterface
     */
    protected function createObjectIdentity($object)
    {
        return ObjectIdentity::fromDomainObject($object);
    }

    /**
     * @param ObjectIdentityInterface $objectIdentity
     * @return mixed|null|AclInterface|MutableAclInterface
     * @throws \Exception
     */
    protected function createAcl(ObjectIdentityInterface $objectIdentity)
    {
        $aclProvider = $this->getAclProvider();

        try
            {
            $acl = $aclProvider->findAcl($objectIdentity);
            } catch (AclNotFoundException $e)
            {
            $acl = $aclProvider->createAcl($objectIdentity);
            }

        return $acl;
    }

    /**
     * @return UserSecurityIdentity
     */
    protected function createSecurityIdentity(\Symfony\Component\Security\Core\User\UserInterface $user = null)
    {
        if (!$user)
            $user = $this->getUser();

        return UserSecurityIdentity::fromAccount($user);
    }

    /**
     * @param MutableAclInterface $acl
     * @param UserSecurityIdentity $securityIdentity
     * @param MutableAclProvider $aclProvider
     * @throws \Exception
     */
    protected function createSecurityEntry(MutableAclInterface $acl, UserSecurityIdentity $securityIdentity, MutableAclProvider $aclProvider, $mask = MaskBuilder::MASK_OWNER
    )
    {
        $acl->insertObjectAce($securityIdentity, $mask);
        $aclProvider->updateAcl($acl);
    }

    /**
     * @param $object
     * @param null $user
     */
    protected function setAclOwner($object, $user = null)
    {
        $objectIdentity = $this->createObjectIdentity($object);

        $this->createSecurityEntry(
                $this->createAcl($objectIdentity), $this->createSecurityIdentity($user), $this->getAclProvider()
        );
    }

    /**
     * @param Request $request
     * @return \AppBundle\Service\Woopra\Event|null
     */
    protected function requestWoopraEvent(Request $request)
    {
        if(null != $id = $request->get('woopra_event')){
            return $this->getWoopraManager()->getEvent($id);
        }

        return null;
    }

    /**
     * @param $name
     * @param array $attributes
     * @return \AppBundle\Service\Woopra\Event
     */
    protected function createWoopraEvent($name, array $attributes = [])
    {
        return $this->getWoopraManager()->createEvent($name, $attributes);
    }

    /**
     * @return \AppBundle\Service\Woopra\Manager|object
     */
    protected function getWoopraManager()
    {
        return $this->get('app.woopra_manager');
    }

    /**
     * @return TokenStorageInterface
     */
    protected function getTokenStorage()
    {
        return $this->get('security.token_storage');
    }

    /**
     * @return \Knp\Component\Pager\PaginatorInterface
     */
    protected function getPaginator()
    {
        return $this->get('knp_paginator');
    }

    /**
     * Override $_GET value | Used by paginator.filter
     */
    protected function overrideGetFilters()
    {
        $request = $this->getCurrentRequest();
        if ($request->query->has('filter_name'))
        {
            $_GET['filter_value'] = '%' . $request->query->get('filter_value') . '%';
        }
    }

    /**
     * @param $index
     */
    protected function incrementAccountIndex($index)
    {
        $account = $this->account();

        return $this->manager('account')->incrementIndex($account, $index);
    }

    /**
     * @return \AppBundle\Service\FileExplorer
     */
    protected function getFileExplorer()
    {
        return $this->get('app.file_explorer');
    }

    /**
     * @return \Symfony\Component\Validator\Validator\RecursiveValidator
     */
    protected function getValidator()
    {
        return $this->get('validator');
    }

    /**
     * @return \AppBundle\Service\SessionStorage
     * 
     */
    protected function getSessionStorage()
    {
        return $this->get('app.session_storage');
    }

    /**
     * @return \AppBundle\Service\UploadHelper
     */
    protected function getUploadHelper()
    {
        return $this->get('app.upload_helper');
    }

    /**
     * @return \AppBundle\Service\DocumentHelper
     */
    protected function getDocumentHelper()
    {
        return $this->get('app.document_helper');
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\CategoryManager
     */
    protected function getCategoryManager()
    {
        return $this->get('sonata.classification.manager.category');
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\ContextManager
     */
    protected function getContextManager()
    {
        return $this->get('sonata.classification.manager.context');
    }

    /**
     * @return \AppBundle\Entity\Component\KitManager
     */
    protected function getKitManager()
    {
        return $this->get('app.kit_manager');
    }

    /**
     * @return \AppBundle\Entity\TeamManager
     */
    protected function getTeamManager()
    {
        return $this->get('app.team_manager');
    }

    /**
     * @return \FOS\UserBundle\Model\UserManager
     */
    protected function getUserManager()
    {
        return $this->get('fos_user.user_manager');
    }

    /**
     * @return \Sonata\MediaBundle\Entity\MediaManager
     */
    protected function getMediaManager()
    {
        return $this->get('sonata.media.manager.media');
    }

    /**
     * @return \AppBundle\Entity\Project\NasaProvider
     */
    protected function getNasaProvider()
    {
        return $this->get('app.nasa_provider');
    }

    /**
     * @deprecated Use $this->account()
     * @return BusinessInterface|null
     */
    protected function getCurrentAccount()
    {
        return $this->account();
    }

    /**
     * @return \AppBundle\Entity\BusinessInterface|\AppBundle\Entity\AccountInterface
     */
    protected function account()
    {
        return $this->member()->getAccount();
    }

    /**
     * @return \AppBundle\Service\NotificationGenerator|object
     */
    protected function getNotificationGenerator()
    {
        return $this->get('app.notification_generator');
    }

    /**
     * @deprecated Use $this->member()
     * @return BusinessInterface|null
     */
    public function getCurrentMember()
    {
        return $this->member();

        /*$user = $this->getUser();

        if ($user instanceof UserInterface) {

            return $user->getInfo();
        }

        throw $this->createAccessDeniedException();*/
    }

    /**
     * @param $id
     * @return object|\AppBundle\Manager\AbstractManager
     */
    protected function manager($id)
    {
        return $this->get(sprintf('%s_manager', $id));
    }

    /**
     * @return BusinessInterface|MemberInterface
     */
    protected function member()
    {
        return $this->user()->getInfo();
    }

    /**
     * @return \AppBundle\Entity\UserInterface|\FOS\UserBundle\Model\UserInterface
     */
    protected function user()
    {
        return $this->getUser();
    }

    /**
     * @param $var
     */
    protected function dd($var)
    {
        function_exists('dump') ? dump($var) : var_dump($var) ;
        die;
    }

}
