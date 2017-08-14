<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Maker;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MakersController extends AbstractApiController
{
    public function getMakersAction(Request $request)
    {
        $context = $request->get('context');

        if(!in_array($context, Maker::getContextList())) {

            $data = 'Invalid maker context';
            $status = Response::HTTP_NOT_FOUND;

        }else{

            $context = $request->get('context');

            $data = $this->manager('maker')->findBy([
                'context' => $context
            ]);

            $status = Response::HTTP_OK;
        }

        $view = View::create($data, $status);

        return $this->handleView($view);
    }
}