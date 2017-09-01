<?php
/**
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */

namespace AppBundle\Service\Notifier;

class Notifier
{
    public function notify (array $notification) {

        $routes = [
            'account_created' => 'integradores/cadastrointegradores/Notificacao',
            'product' => 'tabelabase/tabelabase/Notificacao',
            'proposal_issued' => 'orcamentovendas/orcamentovendas/Notificacao',
            'order_created' => 'orcamentovendas/orcamentovendas/Notificacao'
        ];

        $callback = $notification['Callback'];
        $route = $routes[$callback];

        //$host = getenv('CES_ISQUIK_HOST');
        //$port = getenv('CES_ISQUIK_PORT');
        //$ambience = getenv('CES_ISQUIK_AUTH_USER');
        //$baseUri = "$host:$port";

        $baseUri = 'https://api.isquik.com:443';

        $auth = $this->getToken("$baseUri/auth");
        $this->sendNotification($notification, $auth, "$baseUri/isquik-dev/$route");
    }

    public function getToken ($url)
    {
        /*$params = Array(
            'Chave' => getenv('CES_ISQUIK_AUTH_KEY'),
            'Dominio' => getenv('CES_ISQUIK_AUTH_USER')
        );*/

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
    }

    public function sendNotification ($notification, $auth, $url)
    {
        $notifier = curl_init();
        curl_setopt($notifier, CURLOPT_URL, $url);
        curl_setopt($notifier, CURLOPT_HTTPHEADER, ["Authorization: $auth"]);
        curl_setopt($notifier, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($notifier, CURLOPT_POST, true);
        curl_setopt($notifier, CURLOPT_POSTFIELDS, http_build_query($notification));
        curl_exec($notifier);
        curl_close($notifier);
        return;
    }
}
