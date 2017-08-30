<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\UserInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AppListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var bool
     */
    private $handleExceptions = false;

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
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if(null != $member = $this->getMember()) {
            date_default_timezone_set($member->getTimezone() ?: 'America/Sao_Paulo');
        }
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $eventController = $event->getController();
        $controller = get_class($eventController[0]);
        $action = $eventController[1];

        if(in_array($controller, array_keys($this->disabledControllers))){

            $actions = $this->disabledControllers[$controller];

            if(in_array($action, $actions)) {

                $eventController[1] = 'forceNotFoundException';
                $event->setController($eventController);
            }
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        return;

        $exception = $event->getException();

        $format = $event->getRequest()->attributes->get('_format');

        if($exception && 'json' != $format) {
            /** @var \Twig_Environment $twig */
            $twig = $this->container->get('twig');
            $content = $twig->render('TwigBundle:Exception:error.html.twig', [
                'exception' => $exception
            ]);

            $response = new Response($content);

        }else{

            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $message = 'Falha ao executar a operação';

            if($exception instanceof HttpException){
                $code = $exception->getStatusCode();
                if(Response::HTTP_NOT_FOUND == $code){
                    $message = 'Recurso não encontrado';
                }
            }

            $response = new JsonResponse([
                'code' => $code,
                'message' => $message
            ], $code);
        }

        $event->setResponse($response);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        return;
    }

    /**
     * @return \AppBundle\Entity\BusinessInterface
     */
    public function getAccount()
    {
        $member = $this->getMember();

        return $member instanceof BusinessInterface ? $member->getAccount() : null;
    }

    /**
     * @return \AppBundle\Entity\BusinessInterface|null
     */
    private function getMember()
    {
        $user = $this->getUser();

        return $user instanceof UserInterface ? $user->getInfo() : null ;
    }

    /**
     * @return \AppBundle\Entity\UserInterface
     */
    private function getUser()
    {
        if(null != $token = $this->getTokenStorage()->getToken()){
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
}
