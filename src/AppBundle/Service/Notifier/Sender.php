<?php
/**
 * @author João Zaqueu Chereta <joaozaqueu@kolinalabs.com>
 */

namespace AppBundle\Service\Notifier;

Use  GuzzleHttp\Client;

class Sender
{
    public static function post($url, $data)
    {
        $client = new Client(['base_uri' => $url]);

        $response = $client->request('POST','', [
            'form_params'  => $data
        ]);

        return $response;
    }

}