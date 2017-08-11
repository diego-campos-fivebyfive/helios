<?php
/**
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */

namespace AppBundle\Service\Notifier;

class Notifier
{
    public function notify (array $notification) {

        $getToken = function($url)
        {
            $params = Array(
                'Chave' => '12eb45ec-b98f-4942-9124-b7b6b389088e',
                'Dominio' => 'isquik-dev'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            $auth = curl_exec($ch);
            curl_close($ch);

            $auth = json_decode($auth, true);
            $auth = $auth['access_token'];
            return "Bearer $auth";
        };

        $sendNotification = function($notification, $auth, $url)
        {
            $notifier = curl_init();
            curl_setopt($notifier, CURLOPT_URL, $url);
            curl_setopt($notifier, CURLOPT_HTTPHEADER, ["Authorization: $auth"]);
            curl_setopt($notifier, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($notifier, CURLOPT_TIMEOUT, 3);
            curl_setopt($notifier, CURLOPT_POST, true);
            curl_setopt($notifier, CURLOPT_POSTFIELDS, http_build_query($notification));
            curl_close($notifier);
        };

        $routes = [
            'account_created' => 'integradores/cadastrointegradores/Notificacao'
        ];

        $callback = $notification['Callback'];
        $route = $routes[$callback];

        $auth = $getToken('https://api.isquik.com/auth');
        $sendNotification($notification, $auth, "https://api.isquik.com:443/isquik-dev/$route");
    }
}
