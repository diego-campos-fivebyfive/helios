<?php

namespace ApiBundle\Controller;

use ApiBundle\Form\ModuleType;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class ErrorsController extends AbstractApiController
{
    public function getErrorsAction()
    {
        /** @var RecursiveValidator $validator */
        $validator = $this->get('validator');

        $data = [
            'firstname' => 'Firstname',
            'email' => 'email@teste.com'
        ];

        $errors = $validator->validate($data);

        dump($validator); die;

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

    public function postErrorAction(Request $request)
    {
        $manager = $this->manager('module');

        $module = $manager->create();

        $form = $this->createForm(ModuleType::class);

        $form->handleRequest($request);

        dump($module); die;
    }
}