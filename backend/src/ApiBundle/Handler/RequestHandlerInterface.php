<?php

namespace ApiBundle\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestHandlerInterface
{
    /**
     * @param Request $request
     * @return RequestHandlerInterface
     */
    public function setRequest(Request $request);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param FormInterface $form
     * @return RequestHandlerInterface
     */
    public function setForm(FormInterface $form);

    /**
     * @return FormInterface
     */
    public function getForm();

    /**
     * @param Request|null $request
     * @param FormInterface|null $form
     * @return bool
     */
    public function handle(Request $request = null, FormInterface $form = null);

    /**
     * @return \FOS\RestBundle\View\View
     */
    public function view();

    /**
     * @param Request|null $request
     * @param FormInterface|null $form
     * @return RequestHandlerInterface
     */
    public static function create(Request $request = null, FormInterface $form = null);
}