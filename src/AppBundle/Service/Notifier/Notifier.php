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

        //$port = getenv('ISQUIK_PORT');
        $port = 2021;

        $client = new Client(['base_uri' => "http://localhost:$port"]);

        $response = $client->request('POST','/notifications', [
           'form_params'  => $data
        ]);

        //dump($response->getBody()->getContents());die;
    }
}