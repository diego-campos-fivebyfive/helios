<?php

namespace ApiBundle\Handler;

use FOS\RestBundle\View\View;
use ApiBundle\Model\Error\Normalizer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestHandler implements RequestHandlerInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * RequestHandler constructor.
     * @param Request|null $request
     * @param FormInterface|null $form
     * @param null $entity
     */
    function __construct(Request $request = null, FormInterface $form = null)
    {
        $this->request = $request;
        $this->form = $form;
    }

    /**
     * @inheritDoc
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request = null, FormInterface $form = null)
    {
        if($request) $this->request = $request;
        if($form) $this->form = $form;

        $source = $this->request->isMethod('get') ? $this->request->query : $this->request->request;
        $data = $source->all();

        $this->form->submit($data);

        if($this->form->isSubmitted() && $this->form->isValid()){
            return true;
        }

        return false;
    }

    /**
     * @return \FOS\RestBundle\View\View
     */
    public function view()
    {
        $status = Response::HTTP_OK;
        $data = $this->form->getData();

        if($this->form->getErrors(true)->count()){

            $status = Response::HTTP_BAD_REQUEST;

            $data = [
                'errors' => Normalizer::normalize($this->form),
                'method' => strtolower($this->request->getMethod()),
                'url' => $this->request->getUri()
            ];
        }

        return View::create($data, $status);
    }

    /**
     * @inheritDoc
     */
    public static function create(Request $request = null, FormInterface $form = null)
    {
        return new self($request, $form);
    }
}