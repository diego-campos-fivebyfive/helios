<?php

namespace AppBundle\Service;

use AppBundle\Entity\Extra\EmailHandlerInterface;
use AppBundle\Entity\Extra\EmailHandlerManager;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class EmailHandler
 * @package AppBundle\Service
 */
class EmailHandler
{
    /**
     * @var EmailHandlerManager
     */
    private $manager;

    function __construct(EmailHandlerManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param EmailHandlerInterface $emailHandler
     * @return BinaryFileResponse|RedirectResponse
     */
    public function getResponse(EmailHandlerInterface $emailHandler)
    {
        $this->registerRequest($emailHandler);

        if($emailHandler->isRedirect()){
            return new RedirectResponse($emailHandler->getUrl());
        }

        if($emailHandler->isDownload()){

            $file = new File($emailHandler->getUrl());

            return new BinaryFileResponse($file);
        }

        throw new \InvalidArgumentException('Invalid handler');
    }

    /**
     * @param EmailHandlerInterface $emailHandler
     */
    private function registerRequest(EmailHandlerInterface $emailHandler)
    {
        $emailHandler->nextRequest();

        $this->manager->save($emailHandler);
    }
}