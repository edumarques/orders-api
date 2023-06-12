<?php

declare(strict_types=1);

namespace App\Mapper;

use App\ValueObject\PaginationResponseDataInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface PaginationDataMapperInterface
{
    public static function fromPaginationToPaginationData(
        PaginationInterface $pagination
    ): PaginationResponseDataInterface;
}
