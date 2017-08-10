<?php
/**
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */

namespace AppBundle\Service\Notifier;

class Notifier
{
    public function notify (array $notification) {
        $host = getenv('CES_ISQUIK_HOST');
        $port = getenv('CES_ISQUIK_PORT');

        $routes = [
            'account_created' => 'integradores/cadastrointegradores/Notificacao'
        ];

        $callback = $notification['Callback'];
        $route = $routes[$callback];
        $url = "https://api.isquik.com:443/isquik-dev/$route";
        //$url = "$host:$port/isquik-dev/$route";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($notification));

        $output = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
    }
}
