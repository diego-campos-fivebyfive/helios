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
 * Class StatusChecker
 * This class check order status navigation by
 * 1. current status
 * 2. next status
 * 3. user type - account or platform
 * 4. user roles
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class StatusChecker
{
    /**
     * @param $current
     * @param $next
     * @param $type
     * @param array $roles
     * @return bool
     */
    public static function acceptStatus($current, $next, $type, array $roles = [])
    {
        if ($next != $current) {

            $mapping = self::getMapping();

            if(array_key_exists($current, $mapping)){

                $config = $mapping[$current];

                if(is_bool($config))
                    return $config;

                if(array_key_exists($next, $config)){

                    $rules = $config[$next];

                    if(in_array($type, $rules)){
                        return true;
                    }

                    if(array_key_exists($type, $rules)) {
                        foreach ($rules[$type] as $role){
                            if(in_array($role, $roles)){
                                return true; break;
                            }
                        }
                    }
                }
            }

            return false;
        }

        return true;
    }

    /**
     * This mapping is based on positive checks, so, statuses that do not allow change
     * do not need to be registered as they are denied by default
     *
     * Configuration structure:
     *
     * [
     *    ACCEPTED_CURRENT_STATUS_1 => [
     *      ACCEPTED_NEXT_STATUS_1 => [
     *          ACCEPTED_USER_TYPE_1 => [
     *              ACCEPTED_USER_ROLE_1, ACCEPTED_USER_ROLE_2, ACCEPTED_USER_ROLE_N (optional)
     *          ],
     *          ACCEPTED_USER_TYPE, (directly, without roles)
     *          ACCEPTED_USER_TYPE_N => [ ... ]
     *      ],
     *      ACCEPTED_NEXT_STATUS_N => [ ... ],
     *    ],
     *    ACCEPTED_CURRENT_STATUS_N => [ ... ]
     * ]
     *
     * @return array
     */
    private static function getMapping()
    {
        return [
            OrderInterface::STATUS_BUILDING => [
                OrderInterface::STATUS_PENDING => [
                    UserInterface::TYPE_ACCOUNT
                ],
                OrderInterface::STATUS_VALIDATED => [
                    UserInterface::TYPE_PLATFORM
                ]
            ],
            OrderInterface::STATUS_PENDING => [
                OrderInterface::STATUS_VALIDATED => [
                    UserInterface::TYPE_PLATFORM
                ],
                OrderInterface::STATUS_BUILDING => [
                    UserInterface::TYPE_ACCOUNT,
                    UserInterface::TYPE_PLATFORM
                ]
            ],
            OrderInterface::STATUS_VALIDATED => [
                OrderInterface::STATUS_BUILDING => [
                    UserInterface::TYPE_ACCOUNT,
                    UserInterface::TYPE_PLATFORM
                ],
                OrderInterface::STATUS_APPROVED => [
                    UserInterface::TYPE_ACCOUNT
                ],
                OrderInterface::STATUS_REJECTED => [
                    UserInterface::TYPE_ACCOUNT
                ]
            ],
            OrderInterface::STATUS_APPROVED => [
                OrderInterface::STATUS_REJECTED => [
                    UserInterface::TYPE_PLATFORM => [
                        UserInterface::ROLE_PLATFORM_FINANCIAL
                    ]
                ],
                OrderInterface::STATUS_DONE => [
                    UserInterface::TYPE_PLATFORM => [
                        UserInterface::ROLE_PLATFORM_FINANCIAL
                    ]
                ],
            ]
        ];
    }
}
