<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\EntityInterface;
use App\ValueObject\PaginationResponseDataInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface PaginationDataMapperInterface
{
    /**
     * @param PaginationInterface<EntityInterface> $pagination
     */
    public static function fromPaginationToPaginationData(
        PaginationInterface $pagination
    ): PaginationResponseDataInterface;
}
