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


class Fetcher
{
    /**
     * @var Handler|null
     */
    private $handler;

    /**
     * Fetcher constructor.
     * @param Handler|null $handler
     */
    function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param array $files
     */
    public function fetch(array $files)
    {
        foreach ($files as $file){
            $data = Parser::extract($file);
            //$this->handler->handle($file);
        }
    }
}
