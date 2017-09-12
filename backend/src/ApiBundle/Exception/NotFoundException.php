<?php

namespace ApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct($statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $message = 'OK - Not Found';
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}