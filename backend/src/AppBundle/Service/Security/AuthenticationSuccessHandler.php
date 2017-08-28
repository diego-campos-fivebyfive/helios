<?php

namespace AppBundle\Service\Security;

use AppBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if($request->isXmlHttpRequest()){
            /*$target = 'reload';
            if(UserUtils::getAuthTarget($request) != 'reload')
                $target = $request->getSchemeAndHttpHost();

            return new JsonResponse([
                'status' => 'success',
                'target' => $target
            ]);*/
        }

        /** @var UserInterface $user */
        /*$user = $token->getUser();
        $member = $user->getInfo();
        $account = $member->getAccount();*/
        //dump($account->isLocked()); die;

        return parent::onAuthenticationSuccess($request, $token);
    }
}