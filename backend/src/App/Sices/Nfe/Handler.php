<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Sices\Nfe;

/**
 * Interface Handler
 * This interface provides access by external services to the handling
 * TODO: Esta funcionalidade está em fase de implementação
 */
interface Handler
{
    /**
     * @param $file
     * @return mixed
     */
    public function handle($file);
}
