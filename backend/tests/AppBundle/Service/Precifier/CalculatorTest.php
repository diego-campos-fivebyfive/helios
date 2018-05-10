<?php
/**
 * Created by PhpStorm.
 * User: kolinalabs
 * Date: 5/9/18
 * Time: 2:50 PM
 */

namespace Tests\AppBundle\Service\Precifier;

use AppBundle\Service\Precifier\ComponentsPrecifier;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class PrecifyTest
 * @group new_precify
 */
class PrecifyTest extends WebTestCase
{
    public function testNewPrecify()
    {
        $components = [
            "memorial_id" => 258,
            "level" => 'partner',
            "power" => 78,
            "groups" => [
                'inverter' => [
                    123//,234
                ],
                'module' => [
                    345//,456
                ]
            ]
        ];


        ComponentsPrecifier::precify($components);

    }
}