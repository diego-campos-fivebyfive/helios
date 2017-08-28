<?php

namespace AppBundle\Service\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        /*if($request->isXmlHttpRequest()){
            $errors = [
                'Bad credentials.' => [
                    'pt_BR' => 'Login ou senha inválidos',
                    'en' => 'Bad credentials.'
                ],
                'User account is disabled.' => [
                    'pt_BR' => 'Conta de usuário desativada',
                    'en' => 'User account is disabled'
                ]
            ];

            $message = $exception->getMessage();
            if(array_key_exists($message, $errors)){
                $message = $errors[$message][$request->getLocale()];
            }

            return new JsonResponse([
                'status' => 'error',
                'error' => $message,
                'target' => UserUtils::getAuthTarget($request)
            ]);
        }*/

        return parent::onAuthenticationFailure($request, $exception);
    }
}