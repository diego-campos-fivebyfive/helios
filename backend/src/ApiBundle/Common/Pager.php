<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ApiBundle\Common;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * This class is a conversion service for pagination of results in the API
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Pager
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @param Formatter $formatter
     */
    function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @param PaginationInterface|SlidingPagination $pagination
     * @param Formatter $formatter
     * @return array
     */
    public function paginate(PaginationInterface $pagination, array $converts = [])
    {
        $currentPage = $pagination->getCurrentPageNumber();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $totalCount = $pagination->getTotalItemCount();
        $items = $pagination->getItems();

        if(count($items)) {
            $items = $this->formatter->format($items, $converts);
        }

        return [
            'page' => $currentPage,
            'per_page' => $itemsPerPage,
            'total' => $totalCount,
            'items' => $items
        ];
    }
}