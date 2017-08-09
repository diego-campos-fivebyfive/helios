<?php
/**
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */

namespace AppBundle\Service\Notifier;

class Notifier
{
    public function notify(array $notification, $entrypoint){
        $host = getenv('CES_ISQUIK_HOST');
        $port = getenv('CES_ISQUIK_PORT');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$host:$port/isquik-dev/$entrypoint");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($notification));

        $output = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
    }
}
