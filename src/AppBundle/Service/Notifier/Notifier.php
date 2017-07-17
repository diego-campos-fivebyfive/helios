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
    public function notifier(){

        $port = getenv('ISQUIK_PORT');

        $client = new Client(['base_uri' => "http://localhost:$port"]);

        $response = $client->post('/notifications', [
           'callback' => 'product_create',
           'body'  => 'id'
        ]);
    }
}