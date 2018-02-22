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

use AppBundle\Manager\OrderManager;

class Processor
{
    /**
     * @var OrderManager
     */
    private $manager;

    /**
     * Processor constructor.
     * @param OrderManager $manager
     */
    function __construct(OrderManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $files
     * @return mixed
     */
    public function indexer(array $files)
    {
        return array_reduce($files, function ($carry, $file) {
            if (!strpos($file, '.')) {
                return $carry;
            }

            list($name, $extension) = explode('.', $file);
            $carry[$name][] = $extension;
            return $carry;
        }, []);
    }


    public function matchReference($danfe)
    {
        return $this->manager->findOneBy([
            'billedAt' => null,
            'reference' => $danfe['reference']
        ]);
    }
}
