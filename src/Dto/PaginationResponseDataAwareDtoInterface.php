<?php

declare(strict_types=1);

namespace App\Dto;

use App\ValueObject\PaginationResponseDataInterface;

interface PaginationResponseDataAwareDtoInterface
{
    public function getPaginationResponseData(): PaginationResponseDataInterface;

    public function setPaginationResponseData(PaginationResponseDataInterface $paginationResponseData): static;
}
