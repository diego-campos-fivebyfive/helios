<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 20/06/2017
 * Time: 15:55
 */

namespace Tests\AppBundle\Util\KitGenerator;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Util\KitGenerator\PowerEstimator;

/**
 * Class PowerEstimatorTest
 * @group power_estimate
 */

class PowerEstimatorTest extends WebTestCase
{
           public function testFactorial(){
              $this->assertEquals(12, PowerEstimator::estimate(3,4, 55, 12));
           }
}