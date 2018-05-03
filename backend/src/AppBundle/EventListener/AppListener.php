<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\Business\TermsChecker;
use AppBundle\Service\Slack\ExceptionNotifier;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class AppListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var bool
     */
    private $handleExceptions = true;

    /**
     * @var array
     */
    private $disabledControllers = [
        \AppBundle\Controller\RegisterController::class => [
        ]
    ];

    /**
     * AppListener constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        if('dev' === $container->get('kernel')->getEnvironment()){
            $this->handleExceptions = false;
        }
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (null != $member = $this->getMember()) {
            date_default_timezone_set($member->getTimezone() ?: 'America/Sao_Paulo');
        }

        $this->checkTerms($event);
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $eventController = $event->getController();
        $controller = get_class($eventController[0]);
        $action = $eventController[1];

        if (in_array($controller, array_keys($this->disabledControllers))) {

            $actions = $this->disabledControllers[$controller];

            if (in_array($action, $actions)) {

                $eventController[1] = 'forceNotFoundException';
                $event->setController($eventController);
            }
        }
    }

    /**
     * @param GetResponseForExceptionEvent $event
     * @throws \Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if(!$this->handleExceptions)
            throw $event->getException();

        $this->sendSlackNotification($event->getException());

        $request = $event->getRequest();
        $pathInfo = $request->getPathInfo();


        /**
         * Strict check for path info api
         */
        if (0 === strpos($pathInfo, '/api')) return;

        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');
        $content = $twig->render('TwigBundle:Exception:error.html.twig', [
            'exception' => $event->getException()
        ]);

        $response = new Response($content);

        $event->setResponse($response);
    }

    /**
     * @return \AppBundle\Entity\AccountInterface
     */
    public function getAccount()
    {
        $member = $this->getMember();

        return $member instanceof BusinessInterface ? $member->getAccount() : null;
    }

    /**
     * @param GetResponseEvent $event
     */
    private function checkTerms(GetResponseEvent $event)
    {
        $member = $this->getMember();

        $request = $event->getRequest();

        // TODO: tambem nao deverá entrar quando a rota for a dos termos
        if ($member && !$member->isPlatformUser()
            && !$request->isXmlHttpRequest()
            && $request->attributes->get('_route') != "notice_render"
            && $request->getPathInfo() != "/_fragment") {
            $account = $member->getAccount();
            $terms = $account->getTerms();

            /** @var TermsChecker $termsChecker */
            $termsChecker = $this->container->get('terms_checker');

            $uncheckedTerms = $termsChecker->synchronize($terms)->unchecked();

            if (!empty($uncheckedTerms)) {

                if ($member->isMasterOwner()) {

                } else {

                }
                // TODO: mover essa linha para o else
                $url = $this->container->get('router')->generate('notice_render', ['view' => 'terms']);

                $response = new RedirectResponse($url);

                $event->setResponse($response);
            }
        }
    }

    /**
     * @return \AppBundle\Entity\MemberInterface|null
     */
    private function getMember()
    {
        $user = $this->getUser();

        return $user instanceof UserInterface ? $user->getInfo() : null;
    }

    /**
     * @return \AppBundle\Entity\UserInterface
     */
    private function getUser()
    {
        if (null != $token = $this->getTokenStorage()->getToken()) {
            return $token->getUser();
        }

        return null;
    }

    /**
     * @return \FOS\UserBundle\Model\UserManagerInterface|object
     */
    private function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');
    }

    /**
     * @return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface|object
     */
    private function getTokenStorage()
    {
        return $this->container->get('security.token_storage');
    }

    /**
     * @return \AppBundle\Service\Woopra\Manager|object
     */
    protected function getWoopraManager()
    {
        return $this->container->get('app.woopra_manager');
    }

    /**
     * @param \Exception $exception
     */
    private function sendSlackNotification($exception)
    {
        (new ExceptionNotifier($this->container))->notify($exception);
    }
}
