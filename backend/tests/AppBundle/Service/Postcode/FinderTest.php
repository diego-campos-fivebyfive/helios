<?php

namespace Tests\AppBundle\Service\Postcode;

use AppBundle\Service\Postcode\Finder;
use Symfony\Component\HttpFoundation\Request;
use Tests\AppBundle\AppTestCase;

/**
 * @group postcode_finder
 */
class FinderTest extends AppTestCase
{
    /**
     * Create test scenario
     */
    public function testDefaultScenario()
    {
        /** @var Finder $postcodeFinder */
        $postcodeFinder = $this->getContainer()->get('postcode_finder');

        $request = new Request();

        $request->request->add(
            [
                'postcode' => '85015310'
            ]
        );

        $response = $postcodeFinder->searchAndFormat($request);

        $this->assertEquals($response['status'], 200);
        $this->assertEquals($response['state'], 'PR');
        $this->assertEquals($response['city'], 'Guarapuava');
        $this->assertEquals($response['neighborhood'], 'Batel');
        $this->assertEquals($response['street'], 'Rua Dom Álvaro Nunes Cabeza de Vaca');

        $request->request->set('postcode', 'asd');

        $response = $postcodeFinder->searchAndFormat($request);

        $this->assertEquals($response['status'], 404);
    }
}
