<?php

namespace AppBundle\Controller\PublicAccess;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Extra\EmailHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("mailing")
 */
class EmailHandlerController extends AbstractController
{
    /**
     * @Route("/{token}/hd/{tag}", name="handle_email", defaults={"tag":"Inovador+Solar"})
     */
    public function handleAction(EmailHandler $emailHandler)
    {
        /** @var \AppBundle\Service\EmailHandler $handler */
        $handler = $this->get('app.email_handler');

        return $handler->getResponse($emailHandler);
    }
}