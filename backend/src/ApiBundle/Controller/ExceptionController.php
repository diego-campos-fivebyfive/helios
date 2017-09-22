<?php

namespace ApiBundle\Controller;

use ApiBundle\Model\Error\Error;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionController extends AbstractApiController
{
    /**
     * @param \Throwable|\Exception $exception
     * @return Response
     */
    public function showAction($exception)
    {
        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();

        $code = $exception instanceof HttpException ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR ;

        $type = 500 == $code ? 'internal_error' : 'not_found' ;

        $data = [
            'errors' => [
                (new Error($code, $type, $exception->getMessage()))->toArray()
            ],
            'method' => strtolower($request->getMethod()),
            'url' => $request->getUri()
        ];

        /** @var \FOS\RestBundle\View\ViewHandler $viewHandler */
        $viewHandler = $this->get('fos_rest.view_handler');

        $view = View::create();
        $view->setFormat('json');
        $view->setData($data);
        $view->setStatusCode($code);

        return $viewHandler->handle($view);
    }
}
