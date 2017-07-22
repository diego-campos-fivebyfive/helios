<?php
/**
 * Created by PhpStorm.
 * User: kolinalabs
 * Date: 22/07/17
 * Time: 11:11
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