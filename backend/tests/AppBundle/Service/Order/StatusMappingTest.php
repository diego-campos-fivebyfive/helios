<?php

namespace Tests\AppBundle\Service\Order;

use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\Order\StatusMapping;
use Tests\AppBundle\AppTestCase;

/**
 * Class StatusMappingTest
 * @group order_status_mapping
 */
class StatusMappingTest extends AppTestCase
{
    public function testGetPossibilities()
    {
//        $parameters = [
//            'sourceStatus' => OrderInterface::STATUS_BUILDING,
//            'sourceSubStatus',
//            'targetStatus',
//            'targetSubStatus',
//            'type',
//            'role',
//            'previousStatus',
//            'previousSubStatus',
//        ];

        $tests = $this->getTests();

        foreach ($tests as $test) {
            $parameters = $test[0];
            $targetsStatus = $test[1];
            $targetsSubStatus = $test[2];
            $quantity = $test[3];

            $possibilities = StatusMapping::getPossibilities($parameters);
            foreach ($possibilities as $possibility) {
                self::assertEquals(true, in_array($possibility['status'], $targetsStatus));
                self::assertEquals(true, in_array($possibility['substatus'], $targetsSubStatus));

            }
            self::assertEquals($quantity, count($possibilities));
        }
    }

    public function testGetActions()
    {
        $tests = $this->getTests();

        foreach ($tests as $test) {
            if (array_key_exists('action', $test)) {
                $expectedAction = $test['action'];

                $parameters = $test[0];

                $actions = StatusMapping::getPossibilities($parameters, true);

                self::assertEquals(count($expectedAction), count($actions));

                foreach ($actions as $action) {
                    $ok = false;
                    foreach ($expectedAction as $item) {
                        if ($action['status'] === $item[0] &&
                            $action['substatus'] === $item[1]) {
                            $ok = true;
                        }
                    }
                    self::assertEquals(true, $ok);
                }
            }


        }
    }

    /**
     * @return array
     */
    private function getTests()
    {
        return [
            [
                [                                                       // parametros
                    'sourceStatus' => OrderInterface::STATUS_BUILDING,
                    'type' => UserInterface::TYPE_ACCOUNT
                ],
                [OrderInterface::STATUS_PENDING],                       // status aceitos
                [null],                                                 // sub status aceitos
                1,
                'action' => [
                    [OrderInterface::STATUS_PENDING, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_BUILDING,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_VALIDATED
                ],
                [null],
                1,
                'action' => [
                    [OrderInterface::STATUS_VALIDATED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_BUILDING,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_VALIDATED
                ],
                [null],
                1,
                'action' => [
                    [OrderInterface::STATUS_VALIDATED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_BUILDING,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_COMMERCIAL,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_VALIDATED
                ],
                [null],
                1,
                'action' => [
                    [OrderInterface::STATUS_VALIDATED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_PENDING,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_BUILDING,
                    OrderInterface::STATUS_VALIDATED,
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                3,
                'action' => [
                    [OrderInterface::STATUS_VALIDATED, null],
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_PENDING,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_BUILDING,
                    OrderInterface::STATUS_VALIDATED,
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                3,
                'action' => [
                    [OrderInterface::STATUS_VALIDATED, null],
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_PENDING,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_COMMERCIAL,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_BUILDING,
                    OrderInterface::STATUS_VALIDATED,
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                3,
                'action' => [
                    [OrderInterface::STATUS_VALIDATED, null],
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_VALIDATED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_PENDING,
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                2,
                'action' => [
                    [OrderInterface::STATUS_PENDING, null],
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_VALIDATED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_PENDING,
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                2,
                'action' => [
                    [OrderInterface::STATUS_PENDING, null],
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_VALIDATED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_COMMERCIAL,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_PENDING,
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                2,
                'action' => [
                    [OrderInterface::STATUS_PENDING, null],
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_VALIDATED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_ACCOUNT,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_BUILDING,
                    OrderInterface::STATUS_APPROVED,
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                3,
                'action' => [
                    [OrderInterface::STATUS_APPROVED, null],
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_APPROVED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_APPROVED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_APPROVED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_COMMERCIAL,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [null],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null]
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_APPROVED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_FINANCIAL,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                    OrderInterface::STATUS_DONE,
                ],
                [
                    OrderInterface::SUBSTATUS_DONE_CONFIRMED,
                    OrderInterface::SUBSTATUS_DONE_RESERVED,
                ],
                3,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                    [OrderInterface::STATUS_DONE, OrderInterface::SUBSTATUS_DONE_CONFIRMED],
                    [OrderInterface::STATUS_DONE, OrderInterface::SUBSTATUS_DONE_RESERVED],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_APPROVED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_FINANCING,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                    OrderInterface::STATUS_DONE,
                ],
                [
                    OrderInterface::SUBSTATUS_DONE_CONFIRMED,
                    OrderInterface::SUBSTATUS_DONE_RESERVED,
                ],
                3,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                    [OrderInterface::STATUS_DONE, OrderInterface::SUBSTATUS_DONE_CONFIRMED],
                    [OrderInterface::STATUS_DONE, OrderInterface::SUBSTATUS_DONE_RESERVED],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_DONE,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_DONE,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_DONE,
                    //'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_FINANCIAL,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_APPROVED,
                ],
                [
                    null
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_APPROVED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_DONE,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_FINANCING,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_APPROVED,
                ],
                [
                    null
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_APPROVED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_DONE,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_DONE_CONFIRMED,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_AFTER_SALES,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_PRODUCTION,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_PRODUCTION],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_DONE,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_DONE_RESERVED,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_AFTER_SALES,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_RESERVED,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_RESERVED],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    //'sourceSubStatus' => 'any',
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    //'sourceSubStatus' => 'any',
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_INSERTED_PRODUCTION,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_LOGISTIC,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_WAITING_MATERIAL,
                    OrderInterface::SUBSTATUS_INSERTED_ON_BILLING,
                ],
                2,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_WAITING_MATERIAL],
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_ON_BILLING],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_INSERTED_RESERVED,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_LOGISTIC,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_WAITING_MATERIAL,
                    OrderInterface::SUBSTATUS_INSERTED_WAITING_PAYMENT,
                ],
                2,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_WAITING_MATERIAL],
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_WAITING_PAYMENT],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_INSERTED_WAITING_MATERIAL,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_LOGISTIC,
                    'previous' => [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_RESERVED]
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_WAITING_PAYMENT,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_WAITING_PAYMENT],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_INSERTED_WAITING_MATERIAL,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_LOGISTIC,
                    'previous' => [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_PRODUCTION]
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_ON_BILLING,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_ON_BILLING],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_INSERTED_WAITING_PAYMENT,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_AFTER_SALES,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_ON_BILLING,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_ON_BILLING],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_INSERTED_ON_BILLING,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_BILLING,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_INSERTED,
                ],
                [
                    OrderInterface::SUBSTATUS_INSERTED_BILLED,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_INSERTED, OrderInterface::SUBSTATUS_INSERTED_BILLED],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_INSERTED,
                    'sourceSubStatus' => OrderInterface::SUBSTATUS_INSERTED_BILLED,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_EXPEDITION,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_AVAILABLE,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_AVAILABLE, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_AVAILABLE,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_AVAILABLE,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_AVAILABLE,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_EXPEDITION,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_COLLECTED,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_COLLECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_COLLECTED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_MASTER,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],
            [
                [
                    'sourceStatus' => OrderInterface::STATUS_COLLECTED,
                    'sourceSubStatus' => null,
                    'type' => UserInterface::TYPE_PLATFORM,
                    'role' => UserInterface::ROLE_PLATFORM_ADMIN,
                    'previous' => []
                ],
                [
                    OrderInterface::STATUS_REJECTED,
                ],
                [
                    null,
                ],
                1,
                'action' => [
                    [OrderInterface::STATUS_REJECTED, null],
                ]
            ],

        ];
    }
}
