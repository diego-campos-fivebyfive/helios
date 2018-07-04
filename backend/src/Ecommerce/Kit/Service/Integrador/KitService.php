<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecommerce\Kit\Service\Integrador;

use Ecommerce\Kit\Manager\KitManager;
use Knp\Component\Pager\PaginatorInterface;

class KitService
{
    /**
     * @var KitManager
     */
    private $manager;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @inheritDoc
     */
    public function __construct(KitManager $manager, PaginatorInterface $paginator)
    {
        $this->manager = $manager;
        $this->paginator = $paginator;
    }

    public function findAll($page = 1, $perPage = 8)
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->manager->createQueryBuilder();

        $qb
            ->orderBy('k.position', 'asc')
            ->where('k.available = true')
            ->andWhere('k.stock > 0');

        $pagination = $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $perPage
        );

        return $pagination;
    }
}
