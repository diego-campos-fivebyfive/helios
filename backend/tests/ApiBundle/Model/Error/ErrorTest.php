<?php

namespace Tests\ApiBundle\Model\Error;

use ApiBundle\Model\Error\Error;
use Tests\ApiBundle\ApiTestCase;

/**
 * @group api_model_error
 */
class ErrorTest extends ApiTestCase
{
    public function testDefaultScenario()
    {
        $data = [
            'code' => 'E258',
            'type' => 'access_denied',
            'message' => 'Hello Error'
        ];

        $error = new Error();
        $this->assertEquals($data['code'], $error->setCode($data['code'])->getCode());
        $this->assertEquals($data['type'], $error->setType($data['type'])->getType());
        $this->assertEquals($data['message'], $error->setMessage($data['message'])->getMessage());

        $errorAsArray = $error->toArray();

        $this->assertArrayHasKey('code', $errorAsArray);
        $this->assertArrayHasKey('type', $errorAsArray);
        $this->assertArrayHasKey('message', $errorAsArray);
    }
}