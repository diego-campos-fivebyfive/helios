<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\AppBundle\Service\Pricing;


use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class MemorialLoaderTest
 * @group memorial_loader
 */
class MemorialLoaderTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testLoadCurrent()
    {
        $memorialA = $this->createMemorial([
            'version' => 2,
            'startAt' => new \DateTime('-15 days'),
            'endAt' => new \DateTime('15 days'),
            'status' => 1
        ]);
        $this->assertNotNull($memorialA->getId());

        $loader = $this->getContainer()->get('memorial_loader');

        $memorialB = $loader->load();

        $this->assertNull($memorialB);
    }

    private function createMemorial(array $data)
    {
        $manager = $this->getContainer()->get('memorial_manager');
        $memorial = $manager->create();
        self::fluentSetters($memorial,$data);
        $manager->save($memorial);
        return $memorial;
    }
}