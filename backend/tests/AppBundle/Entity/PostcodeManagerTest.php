<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Postcode;
use Tests\AppBundle\AppTestCase;


/**
 * Class PostcodeManagerTest
 * @group postcode_manager
 */
class PostcodeManagerTest extends AppTestCase
{
    public function testPostcodeManager()
    {
        $manager = $this->manager('postcode');

        /** @var Postcode $postcodePrud */
        $postcodePrud = $manager->create();

        $postcodePrud->setId(84400000);
        $postcodePrud->setAttributes([
            'postcode' => '84400-000',
            'state' => 'PR',
            'city' => 'Prudentópolis',
            'neighborhood' => 'bairro',
            'street' => 'interior'
        ]);
        $manager->save($postcodePrud);

        self::assertEquals(84400000, $postcodePrud->getId());
        self::assertEquals('84400-000', $postcodePrud->getAttribute('postcode'));
        self::assertEquals('PR', $postcodePrud->getAttribute('state'));
        self::assertEquals('Prudentópolis', $postcodePrud->getAttribute('city'));
        self::assertEquals('bairro', $postcodePrud->getAttribute('neighborhood'));
        self::assertEquals('interior', $postcodePrud->getAttribute('street'));

        /** @var Postcode $postcodeGua */
        $postcodeGua = $manager->create();
        $postcodeGua->setId('85010-220');
        $postcodeGua->setAttribute('postcode', '85010-220');
        $postcodeGua->setAttribute('state', 'PR');
        $postcodeGua->setAttribute('city', 'Guarpa');
        $postcodeGua->setAttribute('neighborhood', 'centro');
        $postcodeGua->setAttribute('street', 'xavier');
        $manager->save($postcodeGua);

        self::assertEquals(85010220, $postcodeGua->getId());
        self::assertEquals('85010-220', $postcodeGua->getAttribute('postcode'));
        self::assertEquals('PR', $postcodeGua->getAttribute('state'));
        self::assertEquals('Guarpa', $postcodeGua->getAttribute('city'));
        self::assertEquals('centro', $postcodeGua->getAttribute('neighborhood'));
        self::assertEquals('xavier', $postcodeGua->getAttribute('street'));
    }
}
