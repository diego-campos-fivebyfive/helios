<?php

namespace Tests\AppBundle\Service\Util;

use AppBundle\Service\Util\PlatformCounter;
use Tests\AppBundle\AppTestCase;

/**
 * Class PlatformCounterTest
 * @group platform_counter
 */
class PlatformCounterTest extends AppTestCase
{
    public function testDefaultCounter()
    {
        $key = 'orders';

        /** @var PlatformCounter $counter */
        $counter = $this->getContainer()->get('platform_counter');

        $this->assertEquals(1, $counter->next($key));
        $this->assertEquals(2, $counter->next($key));
        $this->assertEquals(3, $counter->next($key));

        $counter->date(new \DateTime('1 day'));

        $this->assertEquals(1, $counter->next($key));
        $this->assertEquals(2, $counter->next($key));
        $this->assertEquals(3, $counter->next($key));
        $this->assertEquals(4, $counter->next($key));

        $counter->date(new \DateTime('2 days'));

        for($i = 0; $i < 10; $i++)
            $this->assertEquals($i+1, $counter->next($key));


        $this->assertEquals(1, $counter->next('projects'));
        $this->assertEquals(11, $counter->next('orders'));

        $counter->date(new \DateTime('1 week'));
        $this->assertEquals(1, $counter->next('projects'));
        $this->assertEquals(1, $counter->next($key));
    }
}
