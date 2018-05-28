<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Proceda;

/**
 * Class Events
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Events
{
    const DELIVERING = ['000'];

    const DELIVERED = ['001', '002', '031', '105'];

    const OTHERS_EVENTS = ['091'];

    const MESSAGES = [
        '000' => 'Processo de Transporte já Iniciado',
        '001' => 'Entrega Realizada Normalmente',
        '002' => 'Entrega Fora da Data Programada',
        '031' => 'Entrega com Indenização Efetuada',
        '091' => 'Entrega Programada',
        '105' => 'Entrega efetuada no cliente pela Transportadora de Redespacho'
    ];

    /**
     * @return array
     */
    public static function acceptedEvents()
    {
        return array_keys(self::MESSAGES);
    }
}
