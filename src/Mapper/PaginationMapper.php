<?php

declare(strict_types=1);

namespace App\Mapper;

use App\ValueObject\PaginationResponseData;
use App\ValueObject\PaginationResponseDataInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

final readonly class PaginationMapper implements PaginationDataMapperInterface
{
    public static function fromPaginationToPaginationData(
        PaginationInterface $pagination
    ): PaginationResponseDataInterface {
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $totalOfItems = $pagination->getTotalItemCount();
        $pageCount = $itemsPerPage !== 0
            ? (int) ceil($totalOfItems / $itemsPerPage)
            : 0;

        return PaginationResponseData::create()
            ->setPageNumber($pagination->getCurrentPageNumber())
            ->setPageSize($itemsPerPage)
            ->setTotalOfItems($totalOfItems)
            ->setNumberOfPages($pageCount);
    }
}
