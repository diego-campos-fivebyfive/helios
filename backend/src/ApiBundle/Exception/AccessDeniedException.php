<?php

namespace ApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AccessDeniedException extends \Exception
{
    /*function __construct($statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        die('OK');
        //parent::__construct($statusCode, $message, $previous, $headers, $code);
    }*/
}