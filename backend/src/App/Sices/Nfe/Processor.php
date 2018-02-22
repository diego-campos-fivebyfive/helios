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


class Processor
{
    public function indexer(array $filesList)
    {
        $arrayIndexed = [];

        foreach ($filesList as $file) {
            $file = explode('.', $file);
            $arrayIndexed[$file[0]][] = $file[1];
        }

        return $arrayIndexed;
    }
}
