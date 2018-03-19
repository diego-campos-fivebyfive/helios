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

        $manager = $this->manager('postcode');

        /** @var Postcode $postcodeTest */
        $postcodeTest = $manager->create();

        $postcodeTest->setId(85015310);
        $postcodeTest->setAttributes([
            'postcode' => '85015-310',
            'state' => 'PR',
            'city' => 'Guarapuava',
            'neighborhood' => 'Batel',
            'street' => 'Rua Dom Álvaro Nunes Cabeza de Vaca'
        ]);
        $manager->save($postcodeTest);
        
        /** @var Finder $postcodeFinder */
        $postcodeFinder = $this->getContainer()->get('postcode_finder');

        $postcode = '85015310';

        $response = $postcodeFinder->find($postcode);

        $this->assertEquals($response['status'], 200);
        $this->assertEquals($response['state'], 'PR');
        $this->assertEquals($response['city'], 'Guarapuava');
        $this->assertEquals($response['neighborhood'], 'Batel');
        $this->assertEquals($response['street'], 'Rua Dom Álvaro Nunes Cabeza de Vaca');

        $newPostcode = $manager->find(85015470);

        $this->assertNull($newPostcode);

        $postcode = '85015470';

        $response = $postcodeFinder->find($postcode);

        $this->assertEquals($response['status'], 200);
        $this->assertEquals($response['state'], 'PR');
        $this->assertEquals($response['city'], 'Guarapuava');
        $this->assertEquals($response['neighborhood'], 'Batel');
        $this->assertEquals($response['street'], 'Rua Celmira Garcia Costa');

        $newPostcode = $manager->find(85015470);

        $this->assertNotNull($newPostcode);

        $postcode = 'asd';

        $response = $postcodeFinder->find($postcode);

        $this->assertEquals($response['status'], 404);
    }
}
