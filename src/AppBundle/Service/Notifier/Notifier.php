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

        $client = new Client(['base_uri' => 'http://localhost:8000/debug/']);

        $response  = $client->request('post', '/range');
    }


}