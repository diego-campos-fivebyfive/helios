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
    const PREVIOUS = 'previous';

    const ACTION = 'action';
    const LABEL = 'label';

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
     * PARAMETERS:
     *
     * 'sourceStatus' => STATUS_SOURCE
     * 'sourceSubStatus' => SUB_STATUS_SOURCE (default: self::NULL, "null")
     * 'type' => USER_TYPE
     * 'role' => USER_ROLE (default: null)
     * 'previous' => [TARGET_STATUS, TARGET_SUB_STATUS] (default: null)
     *
     */

    /**
     * [
     *     STATUS_ORIGEM => [
     *         self::ANY => [ (Em qualquer sub-status de origem)
     *          ...
     *         ],
     *         SUB_STATUS_ORIGEM => [
     *             STATUS_DESTINO => [
     *                 SUB_STATUS_DESTINO => [
     *                     TIPO_DE_USER, (Todas as Roles)
     *                     TIPO_DE_USER_1 => [ (Somente Roles especificadas)
     *                         ROLE_USER_1,
     *                         ROLE_USER_2,
     *                     ],
     *                     self::PREVIOUS => [ (status / sub-status anterior, previousStatus previousSubStatus)
     *                         [STATUS_ANTERIOR, SUB_STATUS_ANTERIOR],
     *                         [STATUS_ANTERIOR_2],
     *                         [STATUS_ANTERIOR_1, SUB_STATUS_ANTERIOR_1]
     *
     *                     ]
     *                 ]
     *             ]
     *         ],
     *     ],
     *     STATUS_ORIGEM_2 => [
     *         self::NULL => [ (Sub-status de origem nulo)
     *             STATUS_DESTINO => [
     *                 self::NULL => [ (Sub-status de destino nulo)
     *                     ...
     *                 ],
     *             ]
     *         ]
     *     ]
     * ]
     */

    private static $mapping = [
        self::BUILDING => [
            self::NULL => [
                self::PENDING => [
                    self::NULL => [
                        self::ACCOUNT
                    ],
                    self::ACTION => [
                        self::LABEL => 'Enviar solicitação para SICES'
                    ]
                ],
                self::VALIDATED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Validar Orçamento'
                    ]
                ]
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
                    ],
                    self::ACTION => [
                        self::LABEL => 'Validar Orçamento'
                    ]
                ],
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                            self::COMMERCIAL
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Cancelar Orçamento'
                    ]
                ]
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
                    ],
                    self::ACTION => [
                        self::LABEL => 'Remover Validação'
                    ]
                ],
                self::APPROVED => [
                    self::NULL => [
                        self::ACCOUNT
                    ],
                    self::ACTION => [
                        self::LABEL => 'Aprovar Orçamento'
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
                    ],
                    self::ACTION => [
                        self::LABEL => 'Cancelar Orçamento'
                    ]
                ]
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
                    ],
                    self::ACTION => [
                        self::LABEL => 'Cancelar Orçamento'
                    ]
                ],
                self::DONE => [
                    self::DONE_CONFIRMED => [
                        self::PLATFORM => [
                            self::FINANCIAL,
                            self::FINANCING,
                        ],
                        self::ACTION => [
                            self::LABEL => 'Pagamento Confirmado'
                        ]
                    ],
                    self::DONE_RESERVED => [
                        self::PLATFORM => [
                            self::FINANCIAL,
                            self::FINANCING,
                        ],
                        self::ACTION => [
                            self::LABEL => 'Confirmar Reserva'
                        ]
                    ]
                ]
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
                    ],
                    self::ACTION => [
                        self::LABEL => 'Cancelar Orçamento'
                    ]
                ],
                self::APPROVED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::FINANCIAL,
                            self::FINANCING,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Remover Confirmação'
                    ]
                ]
            ],
            self::DONE_CONFIRMED => [
                self::INSERTED => [
                    self::INSERTED_PRODUCTION => [
                        self::PLATFORM => [
                            self::AFTER_SALES,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Iniciar Produção'
                    ]
                ]
            ],
            self::DONE_RESERVED => [
                self::INSERTED => [
                    self::INSERTED_RESERVED => [
                        self::PLATFORM => [
                            self::AFTER_SALES,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Iniciar Produção de Reserva'
                    ]
                ]
            ]
        ],
        self::INSERTED => [
            self::ANY => [
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Cancelar Orçamento'
                    ]
                ]
            ],
            self::INSERTED_PRODUCTION => [
                self::INSERTED => [
                    self::INSERTED_WAITING_MATERIAL => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::ACTION => [
                            self::LABEL => 'Aguardando Material'
                        ]
                    ],
                    self::INSERTED_ON_BILLING => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::ACTION => [
                            self::LABEL => 'Produção Concluída'
                        ]
                    ]
                ]
            ],
            self::INSERTED_RESERVED => [
                self::INSERTED => [
                    self::INSERTED_WAITING_MATERIAL => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::ACTION => [
                            self::LABEL => 'Aguardando Material'
                        ]
                    ],
                    self::INSERTED_WAITING_PAYMENT => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::ACTION => [
                            self::LABEL => 'Produção Concluída'
                        ]
                    ]
                ]
            ],
            self::INSERTED_WAITING_MATERIAL => [
                self::INSERTED => [
                    self::INSERTED_WAITING_PAYMENT => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::PREVIOUS => [
                            [self::INSERTED, self::INSERTED_RESERVED]
                        ],
                        self::ACTION => [
                            self::LABEL => 'Produção Concluída'
                        ]
                    ],
                    self::INSERTED_ON_BILLING => [
                        self::PLATFORM => [
                            self::LOGISTIC,
                        ],
                        self::PREVIOUS => [
                            [self::INSERTED, self::INSERTED_PRODUCTION]
                        ],
                        self::ACTION => [
                            self::LABEL => 'Produção Concluída'
                        ]
                    ]
                ]
            ],
            self::INSERTED_WAITING_PAYMENT => [
                self::INSERTED => [
                    self::INSERTED_ON_BILLING => [
                        self::PLATFORM => [
                            self::AFTER_SALES,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Pagamento Confirmado'
                    ]
                ]
            ],
            self::INSERTED_ON_BILLING => [
                self::INSERTED => [
                    self::INSERTED_BILLED => [
                        self::PLATFORM => [
                            self::BILLING,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Faturado'
                    ]
                ]
            ],
            self::INSERTED_BILLED => [
                self::AVAILABLE => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::EXPEDITION,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Coleta Disponível'
                    ]
                ]
            ]
        ],
        self::AVAILABLE => [
            self::NULL => [
                self::REJECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::MASTER,
                            self::ADMIN,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Cancelar Orçamento'
                    ]
                ],
                self::COLLECTED => [
                    self::NULL => [
                        self::PLATFORM => [
                            self::EXPEDITION,
                        ]
                    ],
                    self::ACTION => [
                        self::LABEL => 'Coletado'
                    ]
                ]
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
                    ],
                    self::ACTION => [
                        self::LABEL => 'Cancelar Orçamento'
                    ]
                ]
            ]
        ]
    ];

    /**
     * @param $parameters
     * @return array
     */
    public static function getPossibilities($parameters, $getActions = false)
    {
        $possibilities = [];

        if (array_key_exists($parameters['sourceStatus'], self::$mapping)) {
            $sourceSubStatus = array_key_exists('sourceSubStatus', $parameters) && $parameters['sourceSubStatus'] !== null  ? $parameters['sourceSubStatus'] : self::NULL;
            self::fromSubStatus($parameters, $sourceSubStatus, $possibilities, $getActions);
        }

        return $possibilities;
    }

    /**
     * @param $config
     * @param $parameters
     * @return bool
     */
    private static function validatePrevious($config, $parameters)
    {
        if (array_key_exists(self::PREVIOUS, $config)) {
            $previousStatus = array_key_exists('previous', $parameters) ? $parameters['previous'][0] : null;
            $previousSubStatus = array_key_exists('previous', $parameters) ? $parameters['previous'][1] : null;

            foreach ($config[self::PREVIOUS] as $previous) {
                if (array_key_exists(1, $previous)) {
                    if($previous[0] === $previousStatus && $previous[1] === $previousSubStatus){
                        return true;
                        break;
                    }
                } else {
                    if($previous[0] === $previousStatus){
                        return true;
                        break;
                    }
                }
            }

            return false;
        }

        return true;
    }

    /**
     * @param $parameters
     * @param $sourceSubStatus
     * @param $possibilities
     */
    private static function fromSubStatus($parameters, $sourceSubStatus, &$possibilities, $getActions)
    {
        if ($sourceSubStatus != self::ANY) {
            self::fromSubStatus($parameters, self::ANY, $possibilities, $getActions);
        }

        $map = self::$mapping;

        $sourceStatus = $parameters['sourceStatus'];

        if (array_key_exists($sourceSubStatus, $map[$sourceStatus])) {
            $possibleStatus = $map[$sourceStatus][$sourceSubStatus];

            foreach ($possibleStatus as $status => $possibleSubStatus) {

                $genericAction = array_key_exists(self::ACTION, $possibleSubStatus) ? $possibleSubStatus[self::ACTION] : null;

                foreach ($possibleSubStatus as $subStatus => $config) {
                    if ($subStatus === self::ACTION) {
                        continue;
                    }

                    $action = array_key_exists(self::ACTION, $config) ? $config[self::ACTION] : $genericAction;

                    if ($getActions && !$action) {
                        continue;
                    }

                    if (!self::validatePrevious($config, $parameters)) {
                        continue;
                    }

                    $type = $parameters['type'];
                    $role = array_key_exists('role', $parameters) ? $parameters['role'] : null;

                    if (in_array($type, $config) || (array_key_exists($type, $config) && in_array($role, $config[$type]))) {
                        $info = [
                            'status' => $status,
                            'substatus' => $subStatus === self::NULL ? null : $subStatus
                        ];
                        if ($getActions) {
                            $info['label'] = $action[self::LABEL];
                            $info['type'] = $type;
                        }
                        $possibilities[] = $info;
                    }
                }
            }
        }
    }
}
