<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\Order\StatusChecker;
use Tests\AppBundle\AppTestCase;

/**
 * Class StatusCheckerTest
 * @group order_status_checker
 */
class StatusCheckerTest extends AppTestCase
{
    public function testAcceptStatus()
    {
        $tests = [
            UserInterface::TYPE_ACCOUNT => [
                'run' => true,
                'cases' => [
                    OrderInterface::STATUS_BUILDING => [
                        [OrderInterface::STATUS_PENDING, [], true],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_PENDING => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_VALIDATED => [
                        [OrderInterface::STATUS_BUILDING, [], true],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_APPROVED, [], true],
                        [OrderInterface::STATUS_REJECTED, [], true],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_APPROVED => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_REJECTED => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_DONE => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false]
                    ]
                ]
            ],
            UserInterface::TYPE_PLATFORM => [
                'run' => true,
                'cases' => [
                    OrderInterface::STATUS_BUILDING => [
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], true],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_PENDING => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], true],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_VALIDATED => [
                        [OrderInterface::STATUS_BUILDING, [], true],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_APPROVED => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false],
                        [OrderInterface::STATUS_REJECTED, [UserInterface::ROLE_PLATFORM_FINANCIAL], true],
                        [OrderInterface::STATUS_DONE, [], false],
                        [OrderInterface::STATUS_DONE, [UserInterface::ROLE_PLATFORM_FINANCIAL], true],
                    ],
                    OrderInterface::STATUS_REJECTED => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_DONE, [], false],
                    ],
                    OrderInterface::STATUS_DONE => [
                        [OrderInterface::STATUS_BUILDING, [], false],
                        [OrderInterface::STATUS_PENDING, [], false],
                        [OrderInterface::STATUS_APPROVED, [], false],
                        [OrderInterface::STATUS_VALIDATED, [], false],
                        [OrderInterface::STATUS_REJECTED, [], false]
                    ]
                ]
            ]
        ];

        foreach ($tests as $type => $config) {

            if (!$config['run']) {
                $this->markTestSkipped();
                continue;
            }

            foreach ($config['cases'] as $current => $cases) {
                foreach ($cases as $case) {
                    list($next, $roles, $expected) = $case;
                    $method = $expected ? 'assertTrue' : 'assertFalse';
                    $this->$method(StatusChecker::acceptStatus($current, $next, $type, $roles));
                }
            }
        }
    }
}
