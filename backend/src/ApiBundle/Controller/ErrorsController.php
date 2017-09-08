<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ErrorsController extends AbstractApiController
{
    public function getErrorsAction()
    {
        //Response::
        throw $this->createAccessDeniedException('Isto é uma exception');

        /*
        return $this->handleView(View::create([
            'errors' => [
                [
                    'code' => 'E258',
                    'type' => 'access_denied',
                    'message' => 'This is a error sample',
                ]
            ],
            'method' => 'get',
            'url' => '/the/url/with/error'
        ]));*/
    }
}