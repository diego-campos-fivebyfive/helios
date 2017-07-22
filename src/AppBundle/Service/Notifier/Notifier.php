<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 14/07/17
 * Time: 14:40
 */

namespace AppBundle\Service\Notifier;

Use  GuzzleHttp\Client;

class Notifier
{
    public function notify(array $data){

        $host = getenv('CES_ISQUIK_HOST');
        $port = getenv('CES_ISQUIK_PORT');

        $client = new Client(['base_uri' => "$host:$port"]);

        $response = $client->request('POST','/notifications', [
           'form_params'  => $data
        ]);

        //dump($response->getBody()->getContents());die;
    }

}
