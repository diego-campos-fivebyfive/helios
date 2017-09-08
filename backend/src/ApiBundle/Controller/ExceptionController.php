<?php

namespace ApiBundle\Controller;

class ExceptionController
{
    public function showAction($exception)
    {
        dump($exception); die;
    }
}