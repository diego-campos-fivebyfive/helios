<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Order;

use AppBundle\Entity\UserInterface;
use AppBundle\Entity\Order\OrderInterface;

/**
 * Class StatusMapping
 * This class contains a status/substatus mapping of orders
 *
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class StatusMapping
{
    // STATUS
    const BUILDING = OrderInterface::STATUS_BUILDING;
    const PENDING = OrderInterface::STATUS_PENDING;
    const VALIDATED = OrderInterface::STATUS_VALIDATED;
    const APPROVED = OrderInterface::STATUS_APPROVED;
    const REJECTED = OrderInterface::STATUS_REJECTED;
    const DONE = OrderInterface::STATUS_DONE;
    const INSERTED = OrderInterface::STATUS_INSERTED;
    const AVAILABLE = OrderInterface::STATUS_AVAILABLE;
    const COLLECTED = OrderInterface::STATUS_COLLECTED;
    const DELIVERING = OrderInterface::STATUS_DELIVERING;
    const DELIVERED = OrderInterface::STATUS_DELIVERED;

    // SUB-STATUS
    const NULL = 'null';
    const ANY = 'any';
    const DONE_CONFIRMED = OrderInterface::SUBSTATUS_DONE_CONFIRMED;
    const DONE_RESERVED = OrderInterface::SUBSTATUS_DONE_RESERVED;
    const INSERTED_PRODUCTION = OrderInterface::SUBSTATUS_INSERTED_PRODUCTION;
    const INSERTED_RESERVED = OrderInterface::SUBSTATUS_DONE_RESERVED;
    const INSERTED_WAITING_MATERIAL = OrderInterface::SUBSTATUS_INSERTED_WAITING_MATERIAL;
    const INSERTED_WAITING_PAYMENT = OrderInterface::SUBSTATUS_INSERTED_WAITING_PAYMENT;
    const INSERTED_ON_BILLING = OrderInterface::SUBSTATUS_INSERTED_ON_BILLING;
    const INSERTED_BILLED = OrderInterface::SUBSTATUS_INSERTED_BILLED;

    // PREVIOUS STATUS/SUB-STATUS
    const PREV_SUB_STATUS = 'prevSubStatus';
    const PREV_STATUS = 'prevStatus';

    // USER TYPE
    const PLATFORM = UserInterface::TYPE_PLATFORM;
    const ACCOUNT = UserInterface::TYPE_ACCOUNT;

    // USER ROLES
    const MASTER = UserInterface::ROLE_PLATFORM_MASTER;
    const ADMIN  = UserInterface::ROLE_PLATFORM_ADMIN;
    const COMMERCIAL = UserInterface::ROLE_PLATFORM_COMMERCIAL;
    const FINANCIAL = UserInterface::ROLE_PLATFORM_FINANCIAL;
    const AFTER_SALES = UserInterface::ROLE_PLATFORM_AFTER_SALES;
    const EXPANSE = UserInterface::ROLE_PLATFORM_EXPANSE;
    const LOGISTIC = UserInterface::ROLE_PLATFORM_LOGISTIC;
    const FINANCING = UserInterface::ROLE_PLATFORM_FINANCING;
    const BILLING = UserInterface::ROLE_PLATFORM_BILLING;
    const EXPEDITION = UserInterface::ROLE_PLATFORM_EXPEDITION;

    /**
     * [
     *     STATUS_ORIGEM => [
     *         SUB_STATUS_ORIGEM => [
     *             STATUS_DESTINO => [
     *                 SUB_STATUS_DESTINO => [
     *                     TIPO_DE_USER, (Todas as Roles)
     *                     TIPO_DE_USER_1 => [ (Somente Roles especificadas)
     *                         ROLE_USER_1,
     *                         ROLE_USER_2,
     *                     ],
     *                     self::PREV_STATUS => [ (status anterior, previousStatus)
     *                         STATUS_ANTERIOR
     *                     ],
     *                     self::PREV_SUB_STATUS => [ (sub-status anterior, previousSubStatus)
     *                         SUB_STATUS_ANTERIOR
     *                     ],
     *                 ]
     *             ]
     *         ],
     *         self::NULL => [ (Sub-status de origem nulo)
     *             STATUS_DESTINO => [
     *                 self::NULL => [ (Sub-status de destino nulo)
     *                     ...
     *                 ],
     *                 self::ANY => [ (Em qualquer sub-status de destino)
     *                     ...
     *                 ],
     *             ]
     *         ],
     *     ]
     * ]
     */

    private $mapping = [
        self::BUILDING => [
            self::NULL => [
                self::PENDING => [
                    self::NULL => [
                        self::ACCOUNT
                    ]
                ],
                self::VALIDATED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ]
                ],
            ]
        ],
        self::PENDING => [
            self::NULL => [
                self::BUILDING => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ]
                ],
                self::VALIDATED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ]
                ],
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ]
                ],
            ]
        ],
        self::VALIDATED => [
            self::NULL => [
                self::BUILDING => [
                    self::NULL => [
                        self::ACCOUNT
                    ]
                ],
                self::PENDING => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ]
                ],
                self::APPROVED => [
                    self::NULL => [
                        self::ACCOUNT
                    ]
                ],
                self::REJECTED => [
                    self::NULL => [
                        self::ACCOUNT,
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ]
                ],
            ]
        ],
        self::APPROVED => [
            self::NULL => [
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL,
                            self::FINANCIAL,
                            self::FINANCING,
                        ]
                    ]
                ],
                self::DONE => [
                    self::DONE_CONFIRMED => [
                        self::PLATFORM => [
                            self::FINANCIAL,
                            self::FINANCING,
                        ]
                    ],
                    self::DONE_RESERVED => [
                        self::PLATFORM => [
                            self::FINANCIAL,
                            self::FINANCING,
                        ]
                    ],
                ],
            ]
        ],
        self::DONE => [
            self::ANY => [
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                        ]
                    ]
                ],
                self::APPROVED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::FINANCIAL,
                            self::FINANCING,
                        ]
                    ]
                ],
            ],
            self::DONE_CONFIRMED => [
                self::INSERTED => [
                    self::INSERTED_PRODUCTION => [
                        self::PLATFORM => [
                            self::AFTER_SALES,
                        ]
                    ]
                ],
            ],
            self::DONE_RESERVED => [
                self::INSERTED => [
                    self::INSERTED_RESERVED => [
                        self::PLATFORM => [
                            self::AFTER_SALES,
                        ]
                    ]
                ],
            ],
        ],
        self::INSERTED => [
            self::ANY => [
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                        ]
                    ]
                ],
            ],
            self::INSERTED_PRODUCTION => [
                self::INSERTED => [
                    self::INSERTED_WAITING_MATERIAL => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ]
                    ],
                    self::INSERTED_ON_BILLING => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ]
                    ],
                ],
            ],
            self::INSERTED_RESERVED => [
                self::INSERTED => [
                    self::INSERTED_WAITING_MATERIAL => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ]
                    ],
                    self::INSERTED_WAITING_PAYMENT => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ]
                    ],
                ],
            ],
            self::INSERTED_WAITING_MATERIAL => [
                self::INSERTED => [
                    self::INSERTED_WAITING_PAYMENT => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::PREV_SUB_STATUS => [
                            self::INSERTED_RESERVED
                        ]
                    ],
                    self::INSERTED_ON_BILLING => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::PREV_SUB_STATUS => [
                            self::INSERTED_PRODUCTION
                        ]
                    ],
                ],
            ],
            self::INSERTED_WAITING_PAYMENT => [
                self::INSERTED => [
                    self::INSERTED_ON_BILLING => [
                        self::PLATFORM => [
                            self::AFTER_SALES,
                        ],
                    ],
                ],
            ],
            self::INSERTED_ON_BILLING => [
                self::INSERTED => [
                    self::INSERTED_BILLED => [
                        self::PLATFORM => [
                            self::BILLING,
                        ],
                    ],
                ],
            ],
            self::INSERTED_BILLED => [
                self::AVAILABLE => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::EXPEDITION,
                        ],
                    ],
                ],
            ],
        ],
        self::AVAILABLE => [
            self::NULL => [
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                        ]
                    ]
                ],
                self::COLLECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::EXPEDITION,
                        ]
                    ]
                ],
            ]
        ],
        self::COLLECTED => [
            self::NULL => [
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                        ]
                    ]
                ],
            ]
        ],
    ];
}
